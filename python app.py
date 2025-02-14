from flask import Flask, request, jsonify, render_template_string
import smtplib
import random
import re  # Importing regex for email validation

app = Flask(__name__)

# Temporary storage for codes (use a database in production)
verification_codes = {}

# Send Email Function
def send_email(to_email, verification_code):
    sender_email = "mshanto213052@bscse.uiu.ac.bd"  # Replace with your UIU email
    sender_password = "your-email-password"  # Replace with your UIU email password

    try:
        # SMTP configuration for Gmail or your email provider
        with smtplib.SMTP("smtp.gmail.com", 587) as server:
            server.starttls()
            server.login(sender_email, sender_password)
            subject = "Your Verification Code"
            body = f"Your verification code is: {verification_code}"
            message = f"Subject: {subject}\n\n{body}"
            server.sendmail(sender_email, to_email, message)
            print(f"Verification code sent to {to_email}")
    except Exception as e:
        print(f"Error sending email: {str(e)}")


@app.route('/')
def index():
    return render_template_string("""
    <!DOCTYPE html>
    <html>
    <head>
        <title>Email Verification</title>
        <script>
            async function sendCode() {
                const email = document.getElementById("uiu-email").value.trim();

                // Updated validation with regex for UIU email format
                if (!email.match(/^.+@uiu\.ac\.bd$/)) {
                    alert("Please enter a valid UIU email address.");
                    return;
                }

                try {
                    const response = await fetch("/send-code", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ email: email }),
                    });
                    const result = await response.json();

                    if (response.ok) {
                        alert("Verification code sent to your email and phone.");
                    } else {
                        alert(result.error);
                    }
                } catch (error) {
                    console.error(error);
                    alert("Failed to send verification code.");
                }
            }

            async function verifyCode(event) {
                event.preventDefault();
                const email = document.getElementById("uiu-email").value.trim();
                const code = document.getElementById("verification-code").value;

                try {
                    const response = await fetch("/verify-code", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ email: email, code: code }),
                    });
                    const result = await response.json();

                    if (response.ok) {
                        alert("Verification successful!");
                    } else {
                        alert(result.error);
                    }
                } catch (error) {
                    console.error(error);
                    alert("Failed to verify the code.");
                }
            }
        </script>
    </head>
    <body>
        <h1>Email Verification System</h1>
        <form id="verification-form" onsubmit="verifyCode(event)">
            <label for="uiu-email">Enter your UIU email:</label><br>
            <input type="email" id="uiu-email" required><br><br>

            <button type="button" onclick="sendCode()">Send Verification Code</button><br><br>

            <label for="verification-code">Enter verification code:</label><br>
            <input type="text" id="verification-code" required><br><br>

            <button type="submit">Verify Code</button>
        </form>
    </body>
    </html>
    """)

@app.route('/send-code', methods=['POST'])
def send_code():
    data = request.json
    email = data.get('email')

    # Validate email format using regex
    if not email or not re.match(r"^[a-zA-Z0-9._%+-]+@uiu\.ac\.bd$", email):  # Proper regex validation for UIU email
        return jsonify({"error": "Invalid email domain"}), 400

    # Generate a 6-digit verification code
    verification_code = str(random.randint(100000, 999999))
    verification_codes[email] = verification_code

    try:
        send_email(email, verification_code)  # Send the code to the email address
        
        # If you want to send the code to your phone too, uncomment the following lines:
        # phone_number = "your-phone-number"  # Replace with your actual phone number
        # carrier_gateway = "your-sms-gateway.com"  # Replace with your carrier's SMS gateway
        # phone_email = f"{phone_number}@{carrier_gateway}"
        # send_email(phone_email, verification_code)  # Send code to phone (email-to-SMS gateway)

        return jsonify({"message": "Verification code sent successfully to your email and phone."}), 200
    except Exception as e:
        return jsonify({"error": f"Error sending email: {str(e)}"}), 500

@app.route('/verify-code', methods=['POST'])
def verify_code():
    data = request.json
    email = data.get('email')
    code = data.get('code')

    # Check if the entered verification code matches the stored code
    if verification_codes.get(email) == code:
        return jsonify({"message": "Verification successful"}), 200
    else:
        return jsonify({"error": "Invalid verification code"}), 400

if __name__ == "__main__":
    app.run(debug=True)
