from flask import Flask, request, jsonify
import mysql.connector
from datetime import datetime

app = Flask(__name__)

# Database Configuration
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "yourpassword",  # Change this to your MySQL password
    "database": "student-loan"   # Ensure this database exists
}

# Database Connection Function
def get_db_connection():
    return mysql.connector.connect(**db_config)

@app.route('/log-failure', methods=['POST'])
def log_failure():
    data = request.json

    # Get data from the JSON request
    student_id = data.get("studentId")
    email = data.get("email")
    verification_code = data.get("verificationCode")
    captcha_attempt = data.get("captcha")
    ip_address = data.get("ipAddress")
    attempt_time = datetime.now()

    # Validate input data
    if not (student_id and email and ip_address):
        return jsonify({"error": "Missing required fields"}), 400

    # Insert data into the database
    try:
        connection = get_db_connection()
        cursor = connection.cursor()

        # SQL query to insert the failure attempt data into the database
        query = """
            INSERT INTO verificationattempts 
            (student_id, email_address, ip_address, verification_code_attempt, captcha_attempt, status, attempt_time)
            VALUES (%s, %s, %s, %s, %s, 'Failed', %s)
        """
        # Execute the query with the provided data
        cursor.execute(query, (student_id, email, ip_address, verification_code, captcha_attempt, attempt_time))
        connection.commit()  # Commit the transaction
        return jsonify({"message": "Failure logged successfully"}), 200
    except mysql.connector.Error as err:
        print(f"Database Error: {err}")
        return jsonify({"error": "Failed to log data"}), 500
    finally:
        # Close database connection
        if connection:
            cursor.close()
            connection.close()

if __name__ == '__main__':
    app.run(debug=True)
