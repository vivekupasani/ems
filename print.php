<?php
include 'config.php';

$conn = connectDB();
// Get employee ID from URL parameter
$emp_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($emp_id) {
    $query = "SELECT * FROM employees WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CES Appointment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .main-container {
            width: 794px;
            /* A4 width at 96 DPI */
            height: 1123px;
            /* A4 height at 96 DPI */
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #000;
            background-color: white;
            position: relative;
        }

        .main-contents {
            /* position: absolute;
            top: 20px;
            left: 20px; */
            width: 100%;
            height: 100%;
            /* padding: 20px; */
            border: 2px solid #000;
            border-radius: 20px;
        }

        .title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .title h1,
        b {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .title hr {
            width: 100%;
            border: 1px solid #131313;
            margin: 1px 0;
        }

        .left,
        .right {
            background-color: #000;
            height: 15px;
            width: 50px;
        }

        .description {
            display: flex;
            margin: 0px 15px;
        }

        .description img {
            height: 50px;
            width: auto;
            margin-top: 20px;
        }

        .description p {
            margin: 0px 15px;
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .description .photo-box {
            width: 380px;
            height: 150px;
            border: 1px solid #000;
            padding: 7px;
            margin-top: 10px;
        }

        .description .photo-box .photo {
            height: 100%;
            width: auto;
            object-fit: cover;
            border: 1px solid #0000006e;
            border-style: dotted;
            border-radius: 10px;
        }

        .emp-code {
            margin: 0px 40px;
            position: relative;
            top: -50px;
            left: 0px;
        }

        .emp-code input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .emp-details {
            margin: 0px 20px;
            position: relative;
            top: -20px;
            left: 0px;
        }

        .emp-details h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .emp-details hr {
            width: 80%;
        }

        .emp-details .details {
            margin-top: 10px;
        }

        .emp-details .details input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .emp-details label {
            margin-top: 10px;
        }

        .emp-details input {
            width: 100%;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .department {
            display: flex;
            margin-top: 15px;
        }

        .department label {
            margin-top: 10px;
        }

        .department span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .department .dept {
            width: 450px;
        }

        .department #loc {
            margin-left: 20px;
        }

        .joiningDate {
            margin-top: 15px;
        }

        .joiningDate label {
            margin-top: 10px;
        }

        .joiningDate span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .joiningDate input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .designation {
            margin-top: 10px;
        }

        .designation label {
            margin-top: 10px;
        }

        .designation span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .designation input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .emp-cat {
            margin-top: 10px;
        }

        .emp-cat label {
            margin-top: 10px;
        }

        .emp-cat span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .emp-cat input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .employee-details {
            margin: 50px 20px;
            position: relative;
            top: -20px;
            left: 0px;
        }

        .employee-details h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .employee-details .details {
            margin-top: 10px;
        }

        .employee-details .details input {
            padding: 5px 0px;
            width: 100%;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .employee-details .details label span {
            margin-left: 50px;
        }

        .employee-details .gender-bg {
            margin-top: 15px;
        }

        .employee-details .gender-bg input[type="checkbox"] {
            margin-right: 5px;
            transform: scale(1.5);
        }

        .employee-details .gender-bg .female {
            margin-left: 25px;
        }

        .employee-details .gender-bg .bg {
            margin-left: 90px;
        }

        .employee-details .gender-bg input {
            padding: 5px 0px;
            /* width: 100%; */
            font-size: 1rem;
            border: none;
            border-bottom: 1.5px dotted #000;
        }

        .nationality {
            margin-top: 10px;
        }

        .nationality input {
            padding: 5px 0px;
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .birthDate {
            margin-top: 10px;
        }

        .birthDate label {
            margin-top: 10px;
        }

        .birthDate span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .birthDate input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .fname {
            margin-top: 10px;
        }

        .fname label {
            margin-top: 10px;
        }

        .fname span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .fname input {
            width: 455px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .mname {
            margin-top: 10px;
        }

        .mname label {
            margin-top: 10px;
        }

        .mname span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .mname input {
            width: 453px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .sname {
            margin-top: 10px;
        }

        .sname label {
            margin-top: 10px;
        }

        .sname span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .sname input {
            width: 450px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .mobile-no {
            margin-top: 10px;
        }

        .mobile-no label {
            margin-top: 10px;
        }

        .mobile-no span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .mobile-no input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .local-res-alt-no {
            margin-top: 10px;
        }

        .local-res-alt-no label {
            margin-top: 10px;
        }

        .local-res-alt-no span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .local-res-alt-no input {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .email {
            margin-top: 10px;
        }

        .email label {
            margin-top: 10px;
        }

        .email span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .email input {
            width: 350px;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            font-size: 1rem;
        }

        .address {
            margin-top: 10px;
        }

        .address label {
            margin-top: 10px;
        }

        .address span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        /* .address input {
            width: 100%;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
        } */

        .address textarea {
            width: 100%;
            border: none;
            border-bottom: 1.5px dotted #000;
            padding: 5px 0px;
            height: auto;
            font-size: 1rem;
        }

        .bank-details {
            margin: 10px 20px;
        }

        .bank-details h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .bank-details hr {
            width: 100%;
        }

        .bank-details .bank {
            margin-top: 10px;
        }

        .bank-details .bank input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .bank-details .bank label {
            margin-top: 10px;
        }

        .bank-details .bank input {
            width: 393px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .bank-details .branch {
            margin-top: 10px;
        }

        .bank-details .branch input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .bank-details .branch label {
            margin-top: 10px;
        }

        .bank-details .branch input {
            width: 380px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .bank-details .acc-no {
            margin-top: 10px;
        }

        .bank-details .acc-no input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .bank-details .acc-no label {
            margin-top: 10px;
        }

        .bank-details .acc-no input {
            width: 360px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .bank-details .ifsc {
            margin-top: 10px;
        }

        .bank-details .ifsc input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .bank-details .ifsc label {
            margin-top: 10px;
        }

        .bank-details .ifsc input {
            width: 400px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .statutory-details {
            margin: 20px 20px 0px 20px;
        }

        .statutory-details h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .statutory-details hr {
            width: 100%;
        }

        .statutory-details .pan {
            margin-top: 10px;
        }

        .statutory-details .pan input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .statutory-details .pan label {
            margin-top: 10px;
        }

        .statutory-details .pan input {
            width: 295px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .statutory-details .aadhar {
            margin-top: 10px;
        }

        .statutory-details .aadhar input {
            padding: 5px 0px;
            font-size: 1rem;
        }

        .statutory-details .aadhar label {
            margin-top: 10px;
        }

        .statutory-details .aadhar input {
            width: 368px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .salary-details {
            margin: 20px 20px 0px 20px;
        }

        .salary-details h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .salary-details hr {
            width: 100%;
        }

        .salary-details p {
            margin-top: 10px;
        }

        .salary-details .salary-cat {
            margin-top: 15px;
        }

        .salary-details .salary-cat label {
            margin-top: 10px;
        }

        .salary-details .salary-cat span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .salary-details .salary-cat input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .salary-details .salary-cat input[type="text"] {
            width: 200px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1.2rem;
            /* padding: 5px 0px; */
        }

        .salary-details .salary-cat #specify {
            margin-left: 200px;
        }

        .time {
            display: flex;
            margin-top: 5px;
        }

        .time label {
            margin-top: 10px;
        }

        .time span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .time input {
            width: 150px;
            /* height: 25px; */
            padding: 4px 0px;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1.2rem;
        }

        .time #tot {
            margin-left: 30px;
        }

        .form-input {
            margin-top: 10px;
        }

        .form-input label {
            margin-top: 10px;
        }

        .form-input span {
            margin-top: 10px;
            padding: 0px 5px;
        }

        .form-input input {
            width: 250px;
            border: none;
            border-bottom: 1.5px dotted #000;
            /* padding: 5px 0px; */
            font-size: 1.2rem;
        }

        .additions-salary {
            margin: 20px 20px 0px 20px;
        }

        .additions-salary h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .additions-salary hr {
            width: 100%;
        }

        .attach-docs {
            margin: 15px 20px;
        }

        .attach-docs h4 {
            color: white;
            width: fit-content;
            background-color: rgba(0, 0, 0, 0.637);
            padding: 0px 10px 0px 5px;
        }

        .attach-docs hr {
            width: 100%;
        }

        .attach-docs input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
            border-bottom: 1.5px dotted #000;
            font-size: 1rem;
        }

        .note {
            margin: 50px 20px 0px 20px;
            text-align: center;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            height: 200px;
            width: 100%;
            /* gap: 50px; */
            row-gap: 50px;
            column-gap: 100px;
            padding: 0px 20px;
            margin: 100px 0px;
        }

        .signatures p {
            margin-top: 5px;
            text-align: center;
        }

        .footer {
            margin: 0px 20px;
            text-align: left;
            font-size: 1rem;
            left: 20px;
            bottom: 4rem;
            position: absolute;
        }

        .footer p {
            font-size: 14px;
            line-height: 1.2rem;
        }

        .links {
            display: flex;
            align-items: center;
        }

        .links span {
            margin-right: 10px;
        }

        .links a {
            margin-right: 10px;
            margin-left: 5px;
            color: rgb(11, 11, 11);
            text-decoration: none;
            font-size: 14px;
        }

        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-buttons button:hover {
            background-color: #45a049;
        }

        .action-buttons button:last-child {
            background-color: #555;
        }

        .action-buttons button:last-child:hover {
            background-color: #333;
        }

        /* Hide buttons when printing */
        @media print {
            .no-print {
                display: none !important;
            }

            /* Ensure form looks good in print */
            .main-container {
                margin: 0;
                border: none;
            }

            /* Force background colors and images to print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Style for readonly inputs */
        input[readonly],
        textarea[readonly] {
            background-color: #f8f8f8;
            color: #333;
            cursor: not-allowed;
            border: none;
            outline: none;
        }

        /* Fix for checkbox styling */
        input[type="checkbox"][readonly] {
            pointer-events: none;
            opacity: 0.7;
        }

        /* Add visual indicator for required fields */
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }

        .main-container {
            width: 794px;
            /* A4 width at 96 DPI */
            min-height: 1000px;
            /* Reduced from 1123px to allow better page breaks */
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #000;
            background-color: white;
            position: relative;
            page-break-after: always;
            /* Force page break after each container */
        }

        /* Last container should not force a page break */
        .main-container:last-of-type {
            page-break-after: auto;
        }

        /* Print-specific styles */
        @media print {

            /* Hide buttons when printing */
            .no-print {
                display: none !important;
            }

            /* Reset margins and padding for print */
            @page {
                size: A4;
                margin: 0.5cm;
            }

            /* Adjust main container for printing */
            .main-container {
                margin: 0;
                padding: 10px;
                border: none;
                page-break-after: always;
                min-height: auto;
                /* Let content determine height */
            }

            /* Last container should not force a page break */
            .main-container:last-of-type {
                page-break-after: auto;
            }

            /* Force background colors and images to print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Prevent unwanted breaks */
            .main-contents {
                page-break-inside: avoid;
            }

            /* Ensure footer stays with its container */
            .footer {
                position: relative;
                bottom: 0;
                margin-top: 20px;
                page-break-before: avoid;
            }

            /* Adjust spacing for signatures section */
            .signatures {
                margin: 40px 0;
                page-break-inside: avoid;
            }

            /* Adjust heights for better fit */
            .photo-box {
                height: 120px;
                /* Reduced from 150px */
            }

            /* Reduce some margins and padding */
            .emp-details,
            .employee-details,
            .bank-details,
            .statutory-details,
            .salary-details,
            .additions-salary {
                margin: 10px 20px;
            }
        }

        /* Button styles remain the same as before */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-buttons button:hover {
            background-color: #45a049;
        }

        .action-buttons button:last-child {
            background-color: #555;
        }

        .action-buttons button:last-child:hover {
            background-color: #333;
        }

        /* Rest of your existing styles remain the same */

        /* Optimize spacing for the third page */
        .attach-docs {
            margin: 15px 20px;
            page-break-before: avoid;
        }

        .note {
            margin: 30px 20px 0px 20px;
            text-align: center;
            page-break-before: avoid;
        }

        /* Make form sections more compact */
        .form-input {
            margin-top: 8px;
        }

        .salary-details h4,
        .additions-salary h4 {
            margin-top: 10px;
        }

        /* Adjust spacing between sections */
        .bank-details,
        .statutory-details,
        .salary-details,
        .additions-salary {
            padding-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="action-buttons no-print">
        <button onclick="window.print()">Print Form</button>
        <button onclick="goBack()">Back to List</button>
    </div>
    <div class="main-container">
        <div class="main-contents">
            <form action="#" class="form-data">
                <div class="header">
                    <div class="title">
                        <div class="left"></div>
                        <h1><b>APPOINTMENT PROCESS (New Payroll System)
                            </b>
                            <hr id="first">
                            <hr id="first">
                        </h1>
                        <div class="right"></div>
                    </div>
                    <div class="description">
                        <img src="ces.png" alt="">
                        <p>All the respective authorities are requested to note that any new
                            appointment done under any CES institutes should be fill this form,
                            approve from CES management and submit to the Central HR
                            department for executing their salary process under New Payroll System.</p>
                        <div class="photo-box">
                            <div class="photo">
                                <img src="cesLogo.png" alt="Employee Photo" style="max-width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>
                    <div class="emp-code">
                        <label for="emp-code">Employee Code : </label>
                        <input type="text" id="emp-code" name="emp-code"
                            value="<?php echo htmlspecialchars($employee['emp_code'] ?? ''); ?>" readonly>
                        <span>(HR Dept.)</span>
                    </div>
                </div>
                <div class="emp-details">
                    <h4>Employer Details : </h4>
                    <hr>
                    <div class="details">
                        <label for="">Name of institute under appointment given (School / College / Other) :
                        </label>
                        <input type="text" id="institute-name" name="institute-name"
                            value="<?php echo htmlspecialchars($employee['institute_name'] ?? ''); ?>" readonly>

                    </div>
                    <div class="department">
                        <label for="">Department</label>
                        <span>:</span>
                        <input class="dept" type="text" id="department" name="department"
                            value="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>" readonly>

                        <label id="loc">Location</label>
                        <span>:</span>
                        <input type="text" id="location" name="location"
                            value="<?php echo htmlspecialchars($employee['location'] ?? ''); ?>" readonly>
                    </div>
                    <div class="joiningDate">
                        <label for="">Date of Joining</label>
                        <span> : </span>
                        <input type="text" id="doj" name="doj"
                            value="<?php echo htmlspecialchars($employee['joining_date'] ?? ''); ?>" readonly>
                    </div>
                    <div class="designation">
                        <label for="">Designation</label>
                        <span> : </span>
                        <input type="text" id="designation" name="designation"
                            value="<?php echo htmlspecialchars($employee['designation'] ?? ''); ?>" readonly>
                    </div>
                    <div class="emp-cat">
                        <label for="">Employee Category</label>
                        <span> : </span>
                        <input type="text" id="emp-category" name="emp-category"
                            value="<?php echo htmlspecialchars($employee['emp_category'] ?? ''); ?>" readonly>
                    </div>
                </div>
                <div class="employee-details">
                    <h4>Employee Details : </h4>
                    <hr>
                    <div class="details">
                        <label for="">Full Name of Employee (in Capital letters) : <span>(As per Aadhar
                                Card)</span></label> <br>
                        <input type="text" id="emp-fullname" name="emp-fullname"
                            value="<?php echo htmlspecialchars($employee['full_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="gender-bg">
                        <label for="">Gender</label>
                        <span> : </span>
                        <label for="male">Male</label>
                        <input type="checkbox" id="male" name="gender" value="Male" <?php echo ($employee['gender'] ?? '') === 'Male' ? 'checked' : ''; ?> readonly>
                        <label class="female" for="female">Female</label>
                        <input type="checkbox" id="female" name="gender" value="Female" <?php echo ($employee['gender'] ?? '') === 'Female' ? 'checked' : ''; ?> readonly>
                        <label class="bg" for="">Blood Group</label>
                        <span> : </span>
                        <input type="text" id="blood-group" name="blood-group"
                            value="<?php echo htmlspecialchars($employee['blood_group'] ?? ''); ?>" readonly>
                    </div>
                    <div class="nationality">
                        <label for="">Nationality</label>
                        <span> : </span>
                        <input type="text" id="nationality" name="nationality"
                            value="<?php echo htmlspecialchars($employee['nationality'] ?? ''); ?>" readonly>
                            <hr color="" style="border:1px dotted">
                    </div>
                    <div class="birthDate">
                        <label for="">Date of Birth</label>
                        <span> : </span>
                        <input type="text" id="dob" name="dob"
                            value="<?php echo htmlspecialchars($employee['dob'] ?? ''); ?>" readonly>
                    </div>
                    <div class="fname">
                        <label for="">Father's Name (Full Name)</label>
                        <span> : </span>
                        <input type="text" id="father-name" name="father-name"
                            value="<?php echo htmlspecialchars($employee['father_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="mname">
                        <label for="">Mother's Name (Full Name)</label>
                        <span> : </span>
                        <input type="text" id="mother-name" name="mother-name"
                            value="<?php echo htmlspecialchars($employee['mother_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="sname">
                        <label for="">Spouse's Name (Full Name)</label>
                        <span> : </span>
                        <input type="text" id="spouse-name" name="spouse-name"
                            value="<?php echo htmlspecialchars($employee['spouse_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="mobile-no">
                        <label for="">Mobile Number</label>
                        <span> : </span>
                        <input type="text" id="mobile-no" name="mobile-no"
                            value="<?php echo htmlspecialchars($employee['mobile_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="local-res-alt-no">
                        <label for="">Local Residence / Alternate Number</label>
                        <span> : </span>
                        <input type="text" id="local-res-alt-no" name="local-res-alt-no"
                            value="<?php echo htmlspecialchars($employee['alt_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="email">
                        <label for="">E-Mail ID (Small Letters)</label>
                        <span> : </span>
                        <input type="text" id="email" name="email"
                            value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>" readonly>
                    </div>
                    <div class="address">
                        <label for="">Complete Residential Address</label>
                        <span> : </span>
                        <textarea name="address" id="address"
                            readonly><?php echo htmlspecialchars($employee['address'] ?? ''); ?></textarea>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="main-container">
        <div class="main-contents">
            <form action="#" class="form-data">
                <div class="bank-details">
                    <h4>Bank Details : </h4>
                    <hr>
                    <div class="bank">
                        <label for="">Bank Name</label>
                        <span> : </span>
                        <input type="text" id="bank-name" name="bank-name"
                            value="<?php echo htmlspecialchars($employee['bank_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="branch">
                        <label for="">Branch Name</label>
                        <span> : </span>
                        <input type="text" id="branch-name" name="branch-name"
                            value="<?php echo htmlspecialchars($employee['branch_name'] ?? ''); ?>" readonly>
                    </div>
                    <div class="acc-no">
                        <label for="">Account Number</label>
                        <span> : </span>
                        <input type="text" id="acc-no" name="acc-no"
                            value="<?php echo htmlspecialchars($employee['account_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="ifsc">
                        <label for="">IFSC Code</label>
                        <span> : </span>
                        <input type="text" id="ifsc" name="ifsc"
                            value="<?php echo htmlspecialchars($employee['ifsc_code'] ?? ''); ?>" readonly>
                    </div>
                </div>
                <div class="statutory-details">
                    <h4>Statutory Details : </h4>
                    <hr>
                    <div class="pan">
                        <label for="">Income Tax Number(PAN)</label>
                        <span> : </span>
                        <input type="text" id="pan" name="pan"
                            value="<?php echo htmlspecialchars($employee['pan_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="aadhar">
                        <label for="">Aadhar Number</label>
                        <span> : </span>
                        <input type="text" id="aadhar" name="aadhar" value="
                        <?php echo htmlspecialchars($employee['aadhar_number'] ?? ''); ?>" readonly>
                    </div>
                </div>
                <div class="salary-details">
                    <h4>Salary Details : (to be flled by Accountant)</h4>
                    <hr>
                    <p>Salary Category (Please Tick) : ✔️</p>
                    <div class="salary-cat">
                        <label for="">ADHOC with PF</label>
                        <span> : </span>
                        <input type="checkbox" id="adhoc-pf" name="salary_category" value="ADHOC With PF" <?php echo ($employee['salary_category'] ?? '') === 'ADHOC With PF' ? 'checked' : ''; ?> readonly>
                    </div>
                    <div class="salary-cat">
                        <label for="">ADHOC Without PF</label>
                        <span> : </span>
                        <input type="checkbox" id="not-adhoc-pf" name="salary_category" value="ADHOC Without PF" <?php echo ($employee['salary_category'] ?? '') === 'ADHOC Without PF' ? 'checked' : ''; ?> readonly>
                    </div>
                    <div class="salary-cat">
                        <label for="">5th Pay</label>
                        <span> : </span>
                        <input type="checkbox" id="5th-pay" name="salary_category" value="5th Pay" <?php echo ($employee['salary_category'] ?? '') === '5th Pay' ? 'checked' : ''; ?> readonly>
                    </div>
                    <div class="salary-cat">
                        <label for="">6th Pay</label>
                        <span> : </span>
                        <input type="checkbox" id="6th-pay" name="salary_category" value="6th Pay" <?php echo ($employee['salary_category'] ?? '') === '6th Pay' ? 'checked' : ''; ?> readonly>
                    </div>
                    <div class="salary-cat">
                        <label for="">Any Other</label>
                        <span> : </span>
                        <input type="checkbox" id="any-other" name="salary_category" value="Any Other" <?php echo ($employee['salary_category'] ?? '') === 'Any Other' ? 'checked' : ''; ?> readonly>
                        <label id="specify" for="">(Please Specify)</label>
                        <span> : </span>
                        <input type="text" id="other-salary-cat" name="other-salary-cat"
                            value="<?php echo htmlspecialchars($employee['other_salary_category'] ?? ''); ?>" readonly>
                    </div>
                    <div class="time">
                        <label for="">Duty Hours (Time)</label>
                        <span>:</span>
                        <input class="dept" type="text" id="duty-Hours" name="duty-Hours"
                            value="<?php echo htmlspecialchars($employee['duty_hours'] ?? ''); ?>" readonly>
                        <label id="tot">Total Hours</label>
                        <span>:</span>
                        <input type="text" id="total-Hours" name="total-Hours"
                            value="<?php echo htmlspecialchars($employee['total_hours'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">If appointed on Hourly basis : No. of hours per Day</label>
                        <span>:</span>
                        <input type="text" id="hours-per-day" name="hours-per-day"
                            value="<?php echo htmlspecialchars($employee['hours_per_day'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Salary Payband (Scale) approved by the CES </label>
                        <span>:</span>
                        <input type="text" id="salary-payband" name="salary-payband"
                            value="<?php echo htmlspecialchars($employee['salary_pay_band'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Basic Salary decided by CES</label>
                        <span>:</span>
                        <input type="text" id="basic-salary" name="basic-salary"
                            value="<?php echo htmlspecialchars($employee['basic_salary'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">PF Account Number (UAN Number)</label>
                        <span>:</span>
                        <input type="text" id="pf-ac-no" name="pf-ac-no"
                            value="<?php echo htmlspecialchars($employee['pf_number'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">PF Date of Join</label>
                        <span>:</span>
                        <input type="text" id="pf-doj" name="pf-doj"
                            value="<?php echo htmlspecialchars($employee['pf_join_date'] ?? ''); ?>" readonly>
                        <span>(DD/MM/YYYY)</span>
                    </div>
                </div>
                <div class="additions-salary">
                    <h4>Additions in salary : (to be filled by Accountant)</h4>
                    <hr>
                    <div class="form-input">
                        <label for="">Conveyance Allowance</label>
                        <span>:</span>
                        <input type="text" id="ca" name="ca"
                            value="<?php echo htmlspecialchars($employee['ca'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Dearness Allowance (DA)</label>
                        <span>:</span>
                        <input type="text" id="da" name="da"
                            value="<?php echo htmlspecialchars($employee['da'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">House Rent Allowance (HRA)</label>
                        <span>:</span>
                        <input type="text" id="hra" name="hra"
                            value="<?php echo htmlspecialchars($employee['hra'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Medical Allowance</label>
                        <span>:</span>
                        <input type="text" id="ma" name="ma"
                            value="<?php echo htmlspecialchars($employee['ma'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Travelling Allowance</label>
                        <span>:</span>
                        <input type="text" id="ta" name="ta"
                            value="<?php echo htmlspecialchars($employee['ta'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-input">
                        <label for="">Other Allowance</label>
                        <span>:</span>
                        <input type="text" id="pa" name="pa"
                            value="<?php echo htmlspecialchars($employee['other_allowance'] ?? ''); ?>" readonly>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="main-container">
        <div class="main-contents">
            <form action="#" class="form-data">
                <div class="attach-docs">
                    <h4>Attach Documents : </h4>
                    <hr>
                    <div class="form-input">
                        <input type="checkbox" id="aadhar-copy" name="aadhar-copy" required value="checked" checked>
                        <span> : </span>
                        <label for="aadhar-copy">2 Copies of Aadhar Card</label>
                    </div>

                    <div class="form-input">
                        <input type="checkbox" id="pan-card" name="pan-card" required checked>
                        <span> : </span>
                        <label for="pan-card">2 Copies of PAN Card</label>
                    </div>

                    <div class="form-input">
                        <input type="checkbox" id="bank-statement" name="bank-statement" required checked>
                        <span> : </span>
                        <label for="bank-statement">1 Copy of Salary Bank Account (Only PNB/SBI Bank)</label>
                    </div>

                </div>
                <div class="note">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate, esse.</p>
                </div>
                <div class="signatures">
                    <div class="signature">
                        <hr>
                        <p>Signature of Institute’s Head with Stamp</p>
                    </div>
                    <div class="signature">
                        <hr>
                        <p>Signature of Employee</p>
                    </div>
                    <div class="signature">
                        <hr>
                        <p>(Name : ________________) Verified by(HR)</p>
                    </div>
                    <div class="signature">
                        <hr>
                        <p>Account Department</p>
                    </div>
                </div>
                <div class="footer">
                    <h3>Charotar Education Society</h3>
                    <p>D.N. High school Campus, Station Road, Anand - 388 001. Gujarat.</p>
                    <p>Ph. No. (02692) 243083</p>
                    <div class="links">
                        <p>E-mail : </p>
                        <a href="">cesociety@cesociety.in</a>
                        <span>●</span>
                        <p>Website : </p>
                        <a href="">www.cesociety.in</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Make all form elements readonly
        const formElements = document.querySelectorAll('input, textarea, select');
        formElements.forEach(element => {
            element.setAttribute('readonly', true);
            if (element.type === 'checkbox') {
                element.onclick = function () { return false; };
            }
        });

        // Prevent form submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.onsubmit = function (e) {
                e.preventDefault();
                return false;
            };
        });
    });

    function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.close(); // Closes the tab if no history is available
    }
}


</script>

</html>