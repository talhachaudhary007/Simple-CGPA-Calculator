<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cgpa_calculator";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semesters = $_POST['semesters'];

    // Insert subjects into the database using prepared statements
    $insertStmt = $conn->prepare("INSERT INTO subjects (semester, subject_name, credit_hours, grade_points) VALUES (?, ?, ?, ?)");
    $insertStmt->bind_param("issd", $semesterNumber, $subjectName, $subjectCredits, $subjectGrade);

    foreach ($semesters as $semesterId => $semester) {
        $semesterNumber = $semesterId;
        foreach ($semester['subjects'] as $subjectId => $subject) {
            $subjectName = $subject['name'];
            $subjectCredits = $subject['credits'];
            $subjectGrade = $subject['grade'];

            // Execute the prepared statement
            if (!$insertStmt->execute()) {
                echo "Error: " . $insertStmt->error;
            }
        }
    }

    // Calculate GPA for each semester and overall CGPA
    $totalCredits = 0;
    $totalGradePoints = 0;
    $gpas = [];

    foreach ($semesters as $semesterId => $semester) {
        $semesterNumber = $semesterId;
        $semesterCredits = 0;
        $semesterGradePoints = 0;

        $sql = "SELECT * FROM subjects WHERE semester = ?";
        $selectStmt = $conn->prepare($sql);
        $selectStmt->bind_param("i", $semesterNumber);
        $selectStmt->execute();
        $result = $selectStmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $credits = $row['credit_hours'];
                $gradePoints = $row['grade_points'];

                $semesterCredits += $credits;
                $semesterGradePoints += $gradePoints * $credits; // Multiply grade points by credit hours
            }

            if ($semesterCredits > 0) {
                $gpa = $semesterGradePoints / $semesterCredits; // Calculate GPA by dividing total grade points by total credit hours
                $gpas[$semesterNumber] = $gpa;

                $totalCredits += $semesterCredits;
                $totalGradePoints += $semesterGradePoints;
            }
        }
    }

    if ($totalCredits > 0) {
        $cgpa = $totalGradePoints / $totalCredits;
    } else {
        $cgpa = 0;
    }

    echo "<h3>GPA for Each Semester</h3>";
    foreach ($gpas as $semester => $gpa) {
        echo "Semester $semester: " . round($gpa, 2) . "<br>"; // Round GPA to two decimal places
    }

    echo "<h3>Overall CGPA</h3>";
    echo "CGPA: " . round($cgpa, 2) . "<br>"; // Round CGPA to two decimal places

    $insertStmt->close();
    $selectStmt->close();
}

$conn->close();
?>
