import mysql.connector
import requests
import json
import os
import re # Added for robust SQL parsing
from mysql.connector import errorcode
from flask import Flask, request, jsonify
from flask_cors import CORS

# --- CONFIGURATION ---
API_KEY = "" # IMPORTANT: Use environment variables in production!
API_URL = "https://openrouter.ai/api/v1/chat/completions"
MODEL_NAME = "deepseek/deepseek-v3.2-exp" # Excellent for complex reasoning and coding tasks

# --- MYSQL CONFIGURATION ---
# *** YOU MUST CHANGE THESE VALUES TO YOUR MYSQL DATABASE DETAILS ***
MYSQL_CONFIG = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'user_registration', # Make sure this is your database nameFope
    'raise_on_warnings': True
}

# --- FLASK APP INITIALIZATION ---
app = Flask(__name__)
CORS(app) # This is crucial to allow requests from your HTML file

# --- HELPER FUNCTIONS ---

def get_mysql_connection():
    """Establishes and returns a connection to the MySQL database."""
    try:
        cnx = mysql.connector.connect(**MYSQL_CONFIG)
        return cnx
    except mysql.connector.Error as err:
        print(f"Database connection error: {err}")
        return None

def get_db_schema():
    """
    Connects to the MySQL DB and extracts schema AND sample data for better LLM context.
    """
    cnx = get_mysql_connection()
    if not cnx:
        return None
    
    cursor = cnx.cursor()
    schema_parts = []
    
    try:
        cursor.execute("SHOW TABLES;")
        table_names = [row[0] for row in cursor.fetchall()]
        
        for table in table_names:
            # 1. Get CREATE TABLE statement (Schema)
            cursor.execute(f"SHOW CREATE TABLE `{table}`;")
            create_statement = cursor.fetchone()[1]
            schema_parts.append(f"--- SCHEMA for table `{table}` ---\n{create_statement}")
            
            # 2. Get Sample Data for Context (The power boost!)
            try:
                cursor.execute(f"SELECT * FROM `{table}` LIMIT 5;")
                columns = [desc[0] for desc in cursor.description]
                rows = cursor.fetchall()
                
                if rows:
                    sample_data = f"\n--- SAMPLE DATA (First 5 rows) for table `{table}` ---\n"
                    sample_data += f"COLUMNS: {', '.join(columns)}\n"
                    for row in rows:
                        # Convert all items to string for clean presentation to the LLM
                        sample_data += str(tuple(str(item) for item in row)) + "\n"
                    schema_parts.append(sample_data)
            except Exception as e:
                # Silently fail if selecting data is an issue (e.g., empty table)
                pass 
            
    except mysql.connector.Error as e:
        print(f"Database error during schema extraction: {e}")
        return None
    finally:
        cursor.close()
        cnx.close()
        
    return "\n\n".join(schema_parts)

def call_llm_api(prompt, temperature=0.1):
    """Generic function to call the OpenRouter API."""
    if not API_KEY:
        raise ValueError("API_KEY not set.")

    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json",
    }
    data = {
        "model": MODEL_NAME,
        "messages": [{"role": "user", "content": prompt}],
        "temperature": temperature,
    }

    try:
        response = requests.post(API_URL, headers=headers, data=json.dumps(data))
        response.raise_for_status()
        result = response.json()
        return result['choices'][0]['message']['content'].strip()
    except requests.exceptions.RequestException as e:
        print(f"API request error: {e}")
        return None
    except (KeyError, IndexError) as e:
        print(f"Error parsing API response: {e}")
        return None

def generate_sql_query(schema, question):
    """
    Sends the schema, sample data, and question to the AI model using a chain-of-thought prompt.
    """
    prompt = f"""
    You are an expert MySQL assistant. Your task is to convert a user's question or command into a valid MySQL query.
    
    CRITICAL INSTRUCTION:
    1. First, provide a **brief reasoning** (Chain-of-Thought) for the query you are about to generate.
    2. Then, provide ONLY the final, clean MySQL query. It must be enclosed in **<SQL>...</SQL>** tags.
    3. For any command that modifies item stock (e.g., 'add 5kg of banana'), generate a single UPDATE query on the inventory_items table, updating the current_stock column.
    4. When identifying a specific item, use **LIKE** for a flexible, case-insensitive match (e.g., WHERE name LIKE '%banana%').
    
    Given the database schema and sample data:
    ---
    {schema}
    ---
    User's question/command: "{question}"

    Provide your reasoning and then the final query inside the tags.
    """

    llm_response = call_llm_api(prompt, temperature=0.1)
    if not llm_response:
        return None

    # Use regex to robustly extract the SQL from the specific tags
    match = re.search(r'<SQL>(.*?)</SQL>', llm_response, re.DOTALL)
    if match:
        return match.group(1).strip()
    
    # Fallback attempt to clean up simple markdown if tags were missed
    if llm_response.startswith("```sql"):
        return llm_response.replace("```sql", "").replace("```", "").strip()

    print(f"WARNING: Could not parse SQL from LLM response. Raw response: {llm_response}")
    return None

def debug_sql_query(schema, question, failed_query, error_message):
    """
    (POWERFUL FEATURE) Sends the failed query and error back to the LLM for correction.
    """
    print(f"Attempting to debug failed query: {failed_query}")
    prompt = f"""
    A user asked: "{question}"
    The database schema is:
    ---
    {schema}
    ---
    
    The previously generated SQL query failed with the following error:
    ---
    Failed Query: {failed_query}
    MySQL Error: {error_message}
    ---
    
    Your task is to debug and provide a **corrected MySQL query**.
    1. Provide a brief explanation of the error.
    2. Then, provide ONLY the corrected, clean MySQL query. It must be enclosed in **<SQL>...</SQL>** tags.
    """
    
    llm_response = call_llm_api(prompt, temperature=0.2) # Higher temp for creativity (debugging)
    if not llm_response:
        return None
        
    match = re.search(r'<SQL>(.*?)</SQL>', llm_response, re.DOTALL)
    if match:
        return match.group(1).strip()
        
    return None


def generate_natural_language_response(question, columns, results):
    """Sends the query results to the AI to get a natural language answer."""
    # ... (This function remains largely the same, but uses the centralized call_llm_api)
    if not API_KEY:
        raise ValueError("API_KEY not set.")

    formatted_results = f"Columns: {', '.join(columns)}\n"
    for row in results:
        formatted_results += str(tuple(str(item) for item in row)) + "\n"

    prompt = f"""
    You are a helpful assistant who provides clear and concise answers.
    A user asked the following question: "{question}"

    The following data was retrieved from a database to answer the question:
    ---
    {formatted_results}
    ---
    Based on this data, please provide a simple, natural language answer to the user's question.
    Do not mention SQL, databases, columns, or rows. Just give the answer.
    For example, if the data is 'count(*): 49', answer 'There are 49 menu items in total.'
    """

    return call_llm_api(prompt, temperature=0.7)


def execute_sql_query(query):
    """Executes the given MySQL query and returns the results or success status."""
    cnx = get_mysql_connection()
    if not cnx:
        # Return the error message directly to be handled by the self-correction logic
        return "ERROR", "Could not connect to the database.", None 
        
    cursor = cnx.cursor()
    
    try:
        cursor.execute(query)

        if cursor.description is not None:
            results = cursor.fetchall()
            column_names = [desc[0] for desc in cursor.description]
            return "SELECT", column_names, results
        else:
            cnx.commit()
            rows_affected = cursor.rowcount
            return "DML/DDL", rows_affected, None
            
    except mysql.connector.Error as e:
        if cnx.is_connected():
            cnx.rollback()
        print(f"SQL execution error: {e.msg}\nFailed query: {query}")
        # Return the specific error message for debugging purposes
        return "ERROR", str(e), query 
    finally:
        if 'cursor' in locals() and cursor:
            cursor.close()
        if 'cnx' in locals() and cnx and cnx.is_connected():
            cnx.close()

# --- LOAD SCHEMA ON STARTUP ---
print("Connecting to MySQL and loading database schema with sample data...")
db_schema = get_db_schema()
if not db_schema:
    print("FATAL: Could not read database schema. The application cannot start.")
    exit()
print("✅ Powerful Schema/Context loaded successfully. The server is ready.")

# --- NEW FLASK API ENDPOINT ---
@app.route('/chat', methods=['POST'])
def chat():
    user_question = request.json.get('query')
    if not user_question:
        return jsonify({'reply': 'Error: No query provided.'}), 400

    # 1. Generate SQL query
    sql_query = generate_sql_query(db_schema, user_question)
    if not sql_query:
        return jsonify({'reply': "Sorry, I couldn't understand that request. Could you please rephrase it?"})

    # 2. Execute the query
    query_type, columns_or_rows_or_error, results_or_failed_query = execute_sql_query(sql_query)

    # 3. Handle Self-Correction (The major power upgrade)
    if query_type == "ERROR":
        error_message = columns_or_rows_or_error
        failed_query = results_or_failed_query
        
        # Attempt to debug/correct the query (one time only)
        corrected_sql = debug_sql_query(db_schema, user_question, failed_query, error_message)
        
        if corrected_sql:
            print(f"✅ Query successfully debugged! Executing corrected query: {corrected_sql}")
            query_type, columns_or_rows_or_error, results_or_failed_query = execute_sql_query(corrected_sql)
            
            # If correction fails again, we give up
            if query_type == "ERROR":
                 return jsonify({'reply': f"I tried to fix the query, but it still failed. Error: {columns_or_rows_or_error.split(':')[-1].strip()}"})

        else:
            # If no correction was generated, return the original error
            return jsonify({'reply': f"Sorry, I couldn't process your request. Error: {error_message.split(':')[-1].strip()}"})

    # 4. Generate the final response
    final_answer = ""
    
    if query_type == "SELECT":
        if not results_or_failed_query:
            final_answer = "I found no results for your query."
        else:
            # columns_or_rows_or_error holds the column names here
            final_answer = generate_natural_language_response(user_question, columns_or_rows_or_error, results_or_failed_query)
            
    elif query_type == "DML/DDL":
        rows_affected = columns_or_rows_or_error
        if 'INSERT' in sql_query.upper():
            final_answer = "✅ Success! A new record has been added."
        elif 'UPDATE' in sql_query.upper():
            final_answer = f"✅ Success! {rows_affected} record(s) have been updated."
        elif 'DELETE' in sql_query.upper():
            final_answer = f"✅ Success! {rows_affected} record(s) have been deleted."
        else:
            final_answer = "✅ Operation successful."
    
    # 5. Send the response back to the frontend
    return jsonify({'reply': final_answer})

# --- RUN THE FLASK APP ---
if __name__ == "__main__":
    # Runs the server on [http://127.0.0.1:5000](http://127.0.0.1:5000). Use host='0.0.0.0' if running in a container or external host
    app.run(debug=True, port=5000)