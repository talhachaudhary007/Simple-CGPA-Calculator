<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semesters = $_POST['semesters'];

    // Calculate GPA for each semester and overall CGPA
    $totalCredits = 0;
    $totalGradePoints = 0;
    $gpas = [];

    foreach ($semesters as $semesterId => $semester) {
        $semesterNumber = $semesterId;
        $semesterCredits = 0;
        $semesterGradePoints = 0;

        foreach ($semester['subjects'] as $subjectId => $subject) {
            $subjectName = $subject['name'];
            $subjectCredits = $subject['credits'];
            $subjectGrade = $subject['grade'];

            $semesterCredits += $subjectCredits;
            $semesterGradePoints += $subjectGrade * $subjectCredits; // Multiply grade points by credit hours
        }

        if ($semesterCredits > 0) {
            $gpa = $semesterGradePoints / $semesterCredits; // Calculate GPA by dividing total grade points by total credit hours
            $gpas[$semesterNumber] = $gpa;

            $totalCredits += $semesterCredits;
            $totalGradePoints += $semesterGradePoints;
        }
    }

    if ($totalCredits > 0) {
        $cgpa = $totalGradePoints / $totalCredits;
    } else {
        $cgpa = 0;
    }

    $response = [
        'gpas' => $gpas,
        'cgpa' => round($cgpa, 2)
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
