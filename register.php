<?php 
// --- BACKEND LOGIC ---
// This backend code is preserved exactly from your original file as requested.
// It is NOT secure and is vulnerable to SQL injection.
// For a production environment, it should be updated with prepared statements.
include "db.php"; 

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $contact_no = $_POST['contact_no'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Error: Passwords do not match!";
    } else {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $check_sql = "SELECT * FROM user_details WHERE username='$username' OR email='$email'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $message = "Error: Username or email already exists!";
        } else {
            // Insert user into the database
            $insert_sql = "INSERT INTO user_details (first_name, last_name, gender, contact_no, age, email, username, password) VALUES ('$first_name', '$last_name', '$gender', '$contact_no', '$age', '$email', '$username', '$hashed_password')";
            
            if ($conn->query($insert_sql) === TRUE) {
                // You might want to redirect the user to a success page or login page
                header("Location: login.php");
                exit();
            } else {
                $message = "Error: " . $insert_sql . "<br>" . $conn->error;
            }
        }
    }
} 
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Registration</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold-accent: #D4AF37;
            --dark-bg: #111827; /* gray-900 */
        }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }

        body {
            background-color: var(--dark-bg);
            background-image: radial-gradient(circle at top right, rgba(212, 175, 55, 0.1), transparent 40%),
                              radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.1), transparent 50%);
        }
        .glass-card {
            background-color: rgba(31, 41, 59, 0.5);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-input:focus {
            border-color: var(--gold-accent) !important;
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.4);
            outline: none;
        }
        .progress-bar-fill {
            transition: width 0.4s ease-in-out;
        }
        .otp-input {
            width: 3rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 0.5rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-step {
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        .form-step.hidden {
            opacity: 0;
            transform: translateX(20px);
            position: absolute;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-inter text-gray-300 min-h-screen flex items-center justify-center p-4">

    <main class="w-full max-w-2xl">
        <div class="glass-card rounded-2xl shadow-2xl p-8 md:p-12">
            <div class="text-center mb-8">
                <h1 class="font-playfair text-4xl font-bold text-white">Create Your Account</h1>
                <p id="step-title" class="mt-3 text-gray-300 transition-opacity duration-300">Step 1 of 2: Personal Details</p>
            </div>

            <div class="w-full bg-gray-700 rounded-full h-2 mb-8">
                <div id="progress-bar" class="bg-gradient-to-r from-amber-400 to-amber-500 h-2 rounded-full progress-bar-fill" style="width: 50%;"></div>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="bg-red-500/20 border border-red-500/30 text-red-300 px-4 py-3 rounded-lg text-center mb-6" role="alert">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <form id="registerForm" action="register.php" method="POST" class="relative overflow-hidden">
                <div id="step-1" class="form-step">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="field-group">
                            <label for="first_name" class="sr-only">First Name</label>
                            <input type="text" name="first_name" id="first_name" required placeholder="First Name" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="field-group">
                            <label for="last_name" class="sr-only">Last Name</label>
                            <input type="text" name="last_name" id="last_name" required placeholder="Last Name" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                         <div class="field-group">
                            <label for="gender" class="sr-only">Gender</label>
                            <select name="gender" id="gender" required class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="field-group">
                             <label for="age" class="sr-only">Age</label>
                            <input type="number" name="age" id="age" required placeholder="Age" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="md:col-span-2 field-group">
                            <label for="contact_no" class="sr-only">Contact Number</label>
                            <input type="tel" name="contact_no" id="contact_no" required placeholder="Contact Number" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                         <div class="md:col-span-2 field-group">
                             <label for="email" class="sr-only">Email Address</label>
                            <input type="email" name="email" id="email" required placeholder="Email Address" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="md:col-span-2 field-group">
                             <label for="username" class="sr-only">Username</label>
                            <input type="text" name="username" id="username" required placeholder="Username" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="field-group">
                            <label for="password" class="sr-only">Password</label>
                            <input type="password" name="password" id="password" required placeholder="Password" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                        <div class="field-group">
                            <label for="confirm_password" class="sr-only">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm Password" class="form-input bg-gray-900/50 border-gray-600 w-full rounded-lg px-4 py-2.5">
                            <p class="text-sm text-red-400 mt-1 h-5 error-message"></p>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button type="button" data-action="next" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition-transform transform hover:scale-105">Next Step</button>
                    </div>
                </div>

                <div id="step-2" class="form-step hidden">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white">Email Verification</h2>
                        <p class="mt-2 text-gray-400">Enter the 6-digit code we sent to <strong id="user-email-display" class="text-amber-300">your email</strong>.</p>
                    </div>
                    <div class="flex justify-center gap-2 my-8 otp-group">
                        <input type="text" name="email_otp_1" maxlength="1" class="otp-input" required>
                        <input type="text" name="email_otp_2" maxlength="1" class="otp-input" required>
                        <input type="text" name="email_otp_3" maxlength="1" class="otp-input" required>
                        <input type="text" name="email_otp_4" maxlength="1" class="otp-input" required>
                        <input type="text" name="email_otp_5" maxlength="1" class="otp-input" required>
                        <input type="text" name="email_otp_6" maxlength="1" class="otp-input" required>
                    </div>
                     <p class="text-sm text-center text-red-400 h-5 error-message" id="email-otp-error"></p>
                    <div class="mt-8 flex gap-4">
                        <button type="button" data-action="back" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg transition">Back</button>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">Verify & Create Account</button>
                    </div>
                </div>
            </form>

            <p class="mt-8 text-center text-sm text-gray-400">
                Already have an account? <a href="login.php" class="font-medium text-blue-400 hover:underline">Sign in</a>
            </p>
        </div>
    </main>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- FORM STEP MANAGEMENT ---
    const steps = document.querySelectorAll('.form-step');
    const nextButton = document.querySelector('[data-action="next"]');
    const backButton = document.querySelector('[data-action="back"]');
    const form = document.getElementById('registerForm');
    const progressBar = document.getElementById('progress-bar');
    const stepTitle = document.getElementById('step-title');

    let currentStep = 1;
    const totalSteps = steps.length;

    const stepTitles = [
        "Step 1 of 2: Personal Details",
        "Step 2 of 2: Email Verification"
    ];

    const updateUI = () => {
        // Update progress bar
        progressBar.style.width = `${(currentStep / totalSteps) * 100}%`;
        
        // Update title
        stepTitle.innerText = stepTitles[currentStep - 1];

        // Show/hide steps
        steps.forEach(step => {
            const stepNumber = parseInt(step.id.split('-')[1]);
            if (stepNumber === currentStep) {
                step.classList.remove('hidden');
            } else {
                step.classList.add('hidden');
            }
        });
    };

    nextButton.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                // In a real app, you would make an API call here to send an email OTP
                console.log('Frontend: Pretending to send email OTP...');
                document.getElementById('user-email-display').innerText = document.getElementById('email').value;
                
                currentStep++;
                updateUI();
            }
        }
    });

    backButton.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateUI();
        }
    });

    // --- CLIENT-SIDE VALIDATION ---
    const fieldValidators = {
        'first_name': val => val ? '' : 'First name is required.',
        'last_name': val => val ? '' : 'Last name is required.',
        'gender': val => val ? '' : 'Gender is required.',
        'age': val => (val && val >= 13) ? '' : 'You must be at least 13.',
        'contact_no': val => /^\+?[0-9\s-]{7,15}$/.test(val) ? '' : 'Valid contact number required.',
        'email': val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) ? '' : 'Valid email required.',
        'username': val => (val.length >= 5) ? '' : 'Username must be at least 5 characters.',
        'password': val => (val.length >= 8) ? '' : 'Password must be at least 8 characters.',
        'confirm_password': val => (val === document.getElementById('password').value) ? '' : 'Passwords do not match.',
    };
    
    const validateStep = (stepNumber) => {
        let isValid = true;
        const stepDiv = document.getElementById(`step-${stepNumber}`);
        
        if (stepNumber === 1) {
            stepDiv.querySelectorAll('input, select').forEach(input => {
                const validator = fieldValidators[input.id];
                const errorMsg = validator ? validator(input.value.trim()) : '';
                const errorEl = input.closest('.field-group').querySelector('.error-message');
                if (errorEl) {
                    errorEl.textContent = errorMsg;
                    if(errorMsg) isValid = false;
                }
            });
        }
        
        if (stepNumber === 2) {
            const otpInputs = stepDiv.querySelectorAll('.otp-input');
            let otp = '';
            otpInputs.forEach(input => otp += input.value);
            const errorEl = stepDiv.querySelector('.error-message');
            if (otp.length !== 6 || !/^\d{6}$/.test(otp)) {
                errorEl.textContent = 'Please enter a valid 6-digit OTP.';
                isValid = false;
            } else {
                 // In a real app, you would make an API call here to verify the OTP.
                 // For now, we assume any 6-digit code is valid for this demo.
                 console.log(`Frontend: Pretending to verify OTP: ${otp}`);
                 errorEl.textContent = '';
            }
        }

        return isValid;
    };

    form.addEventListener('submit', (e) => {
        // Final validation check before submitting the form
        if (!validateStep(currentStep)) {
            e.preventDefault();
        }
    });


    // --- OTP INPUT HANDLING ---
    document.querySelectorAll('.otp-group').forEach(group => {
        const inputs = group.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('keydown', (e) => {
                if (e.key >= 0 && e.key <= 9) {
                    inputs[index].value = ''; // clear current value
                    setTimeout(() => {
                        if (index < inputs.length - 1) inputs[index + 1].focus();
                    }, 10);
                } else if (e.key === 'Backspace') {
                    setTimeout(() => {
                        if (index > 0) inputs[index - 1].focus();
                    }, 10);
                }
            });
        });
    });
});
</script>
</body>
</html>