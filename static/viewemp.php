<?php
include 'config.php';
$conn = connectDB();

// Get all records at once since filtering will be done client-side
$sql = "SELECT * FROM employees";

$result = mysqli_query($conn, $sql);

$result = mysqli_query($conn, "SELECT * FROM employees ");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
     <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <!-- Include DataTables -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <!-- Include XLSX for Excel export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <section id="view-employees" class="section-content p-6">
        <div class="bg-white rounded-lg shadow-md p-6 max-w-[98%] mx-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">View Employees</h2>

            <!-- Search and Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="relative">
                    <input
                        type="text"
                        id="employee-search"
                        placeholder="Search employees..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                </div>

                <div class="flex space-x-4">
                    <input
                        type="date"
                        id="date-from"
                        class="flex-1 px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <input
                        type="date"
                        id="date-to"
                        class="flex-1 px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-end">
                    <button 
                        id="exportButton"
                        onclick="exportToExcel()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export to Excel
                    </button>
                </div>
            </div>

            <!-- Filter Dropdowns -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                <div>
                    <select id="filter-department" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Department</option>
                        <option value="it">IT</option>
                        <option value="hr">HR</option>
                        <option value="finance">Finance</option>
                    </select>
                </div>
                <div>
                    <select id="filter-designation" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Designation</option>
                        <option value="manager">Manager</option>
                        <option value="hod">HOD</option>
                        <option value="analyst">Analyst</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <select id="filter-location" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Location</option>
                    <option value="dn">DN Campus</option>
                    <option value="mogri">Mogri Campus</option>
                    <option value="khetiwadi">Khetiwadi Campus</option>
                    <option value="mbpatel">MB Patel Science College Campus</option>
                    </select>
                </div>
                <div>
                    <select id="filter-category" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Category</option>
                    <option value="permanent">Permanent</option>
                    <option value="adhoc">Adhoc</option>
                    </select>
                </div>
                <div>
                    <select id="filter-salary" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Salary Category</option>
                    <option value="adhoc_with_pf">Adhoc with PF</option>
                    <option value="adhoc_without_pf">Adhoc without PF</option>
                    <option value="5th_pay">5th Pay</option>
                    <option value="6th_pay">6th Pay</option>
                    </select>
                </div>
                
            </div>
            

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="employeeTable" class="min-w-full bg-white border rounded-lg">
            <thead class="bg-gray-50">
            <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institute Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joining Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">leaving Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DOB</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Father Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mother Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spouse Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alt Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IFSC Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAN Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aadhar Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duty Hours</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hours</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours per Day</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Payband</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PF Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PF Join Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conveyance Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DA</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HRA</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medical Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Travelling Allowance</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Allowance</th>
            </tr>
            </thead>
                    <tbody class="divide-y divide-gray-200" id="employeeTableBody">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <button 
                                                onclick="editEmployee(<?= $row['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </button>
                                            <button 
                                                onclick="deleteEmployee(<?= $row['id'] ?>)"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                Delete
                                            </button>
                                            <button 
                                            onclick="window.open('print.php?id=<?php echo $row['id']; ?>&print=true', '_blank'); return false;"
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-red-200 transition-colors">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                Print
                                            </button>
                                            <?php if ($row['approval_status'] != 'approved'): ?>
                                            <button 
                                            onclick="openApprovalModal(<?= $row['id'] ?>)"
                                            class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition-colors">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Approve
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($row['approval_status'] == 'approved'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['emp_code'] ?? '') ?></td> 
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['institute_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['department'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['designation'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['location'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['joining_date'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['leaving_date'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['emp_category'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['full_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['gender'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['blood_group'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['nationality'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['dob'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['father_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['mother_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['spouse_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['mobile_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['alt_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['email'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['address'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['bank_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['branch_name'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['account_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ifsc_code'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pan_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['aadhar_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['salary_category'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['duty_hours'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['total_hours'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['hours_per_day'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['salary_pay_band'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['basic_salary'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pf_number'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['pf_join_date'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ca'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['da'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['hra'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ma'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['ta'] ?? '') ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($row['other_allowance'] ?? '') ?></td>
                
                                   
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
                <div id="pageInfo" class="text-sm text-gray-700"></div>
                <div id="pagination" class="flex space-x-2"></div>
            </div>
        </div>
    </section>

    <script>
    // Global variables
    const ROWS_PER_PAGE = 10;
    let currentPage = 1;
    let filteredData = [];
    const allRows = Array.from(document.querySelectorAll('#employeeTableBody tr'));

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        filteredData = allRows;
        updateTable();
        setupEventListeners();
    });

    function setupEventListeners() {
        // Search input with debouncing
        let searchTimeout;
        document.getElementById('employee-search').addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => filterData(), 300);
        });

        // Date filters
        document.getElementById('date-from').addEventListener('change', filterData);
        document.getElementById('date-to').addEventListener('change', filterData);

        // Dropdown filters
        document.getElementById('filter-department').addEventListener('change', filterData);
        document.getElementById('filter-designation').addEventListener('change', filterData);
        document.getElementById('filter-location').addEventListener('change', filterData);
        document.getElementById('filter-category').addEventListener('change', filterData);
        document.getElementById('filter-salary').addEventListener('change', filterData);
        document.getElementById('filter-status').addEventListener('change', filterData);
        // Add other filter listeners similarly
    }

    function filterData() {
        const searchTerm = document.getElementById('employee-search').value.toLowerCase();
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;
        const department = document.getElementById('filter-department').value.toLowerCase();
        const designation = document.getElementById('filter-designation').value.toLowerCase();
        const location = document.getElementById('filter-location').value.toLowerCase();
        const category = document.getElementById('filter-category').value.toLowerCase();
        const salary = document.getElementById('filter-salary').value.toLowerCase();
        const status = document.getElementById('filter-status').value.toLowerCase();
        // Add other filters similarly

        filteredData = allRows.filter(row => {
            const text = row.textContent.toLowerCase();
            const rowDepartment = row.children[2].textContent.toLowerCase();
            const rowDesignation = row.children[3].textContent.toLowerCase();
            const rowLocation = row.children[4].textContent.toLowerCase();
            const rowCategory = row.children[7].textContent.toLowerCase(); 
            const rowSalary = row.children[26].textContent.toLowerCase();
            const rowStatus = row.children[35].textContent.toLowerCase();
            const joiningDate = row.children[5].textContent; // Adjust index based on your table structure

            let matchesSearch = searchTerm === '' || text.includes(searchTerm);
            let matchesDepartment = department === '' || rowDepartment === department;
            let matchesDesignation = designation === '' || rowDesignation === designation;
            let matchesLocation = location === '' || rowLocation === location;
            let matchesCategory = category === '' || rowCategory === category;
            let matchesSalary = salary === '' || rowSalary === salary;
            let matchesStatus = status === '' || rowStatus === status;
            let matchesDate = true;

            if (dateFrom && dateTo) {
                const date = new Date(joiningDate);
                const from = new Date(dateFrom);
                const to = new Date(dateTo);
                matchesDate = date >= from && date <= to;
            }

            return matchesSearch && matchesDepartment && matchesDesignation && matchesLocation && matchesCategory && matchesSalary && matchesStatus && matchesDate;
        });

        currentPage = 1;
        updateTable();
    }

    function updateTable() {
        const startIndex = (currentPage - 1) * ROWS_PER_PAGE;
        const endIndex = startIndex + ROWS_PER_PAGE;
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);

        // Hide all rows
        allRows.forEach(row => row.style.display = 'none');

        // Show filtered rows for current page
        filteredData.slice(startIndex, endIndex).forEach(row => row.style.display = '');

        updatePagination(totalPages);
        updatePageInfo(startIndex, endIndex);
    }

    function updatePagination(totalPages) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = `px-3 py-1 rounded-md ${i === currentPage ? 
                'bg-blue-600 text-white' : 
                'bg-gray-200 text-gray-700 hover:bg-gray-300'}`;
            button.onclick = () => {
                currentPage = i;
                updateTable();
            };
            pagination.appendChild(button);
        }
    }

    function updatePageInfo(startIndex, endIndex) {
        const pageInfo = document.getElementById('pageInfo');
        pageInfo.textContent = `Showing ${startIndex + 1} to ${Math.min(endIndex, filteredData.length)} of ${filteredData.length} entries`;
    }

    function editEmployee(id) {
        window.location.href = `update.php?id=${id}`;
    }

    function deleteEmployee(id) {
            var result = confirm('Are you sure you want to delete this employee?');
            if (result) {
                window.location.href = 'delete.php?id=' + id + '&confirm=true';
            }
        }

   
    // Function to export employee data to Excel
function exportToExcel() {
  // Get all visible rows from the table (respecting current filters)
  const table = document.getElementById('employeeTable');
  const rows = Array.from(table.querySelectorAll('tbody tr')).filter(row => 
    row.style.display !== 'none'
  );
  
  // Get headers from the table
  const headers = Array.from(table.querySelectorAll('thead th')).map(th => 
    th.textContent.trim()
  ).filter(header => 
    header !== 'Actions' // Exclude the Actions column
  );

  // Create data array for Excel
  const data = [headers];
  
  // Add visible row data
  rows.forEach(row => {
    const rowData = Array.from(row.querySelectorAll('td')).map(td => 
      td.textContent.trim()
    );
    // Remove the last column (Actions column)
    rowData.pop();
    data.push(rowData);
  });

  // Create worksheet
  const ws = XLSX.utils.aoa_to_sheet(data);

  // Set column widths
  const colWidths = headers.map(() => ({ wch: 15 })); // Default width of 15 for all columns
  ws['!cols'] = colWidths;

  // Create workbook
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Employee Data");

  // Generate filename with current date
  const date = new Date().toISOString().split('T')[0];
  const fileName = `Employee_Data_${date}.xlsx`;

  // Save file
  try {
    XLSX.writeFile(wb, fileName);
  } catch (error) {
    console.error('Error exporting to Excel:', error);
    alert('An error occurred while exporting to Excel. Please try again.');
  }
}

// Add event listener to export button
document.getElementById('exportButton').addEventListener('click', exportToExcel);
    </script>
    <!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Approve Employee</h3>
            <div class="mt-4">
                <form id="approvalForm">
                    <input type="hidden" id="approvalEmployeeId" name="employee_id">
                    <div class="mb-4">
                        <label for="newEmployeeCode" class="block text-sm font-medium text-gray-700">
                            Employee Code
                        </label>
                        <input 
                            type="text" 
                            id="newEmployeeCode" 
                            name="new_employee_code" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required
                        >
                    </div>
                    <div class="flex justify-between mt-4">
                        <button 
                            type="button" 
                            onclick="cancelApproval()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                        >
                            Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openApprovalModal(employeeId) {
    document.getElementById('approvalEmployeeId').value = employeeId;
    document.getElementById('approvalModal').classList.remove('hidden');
}

function cancelApproval() {
    document.getElementById('approvalModal').classList.add('hidden');
}

document.getElementById('approvalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Create FormData to send the form data
    var formData = new FormData(this);
    
    // Send AJAX request to approve employee
    fetch('approve_employee.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page or update the row
            location.reload();
        } else {
            alert('Approval failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during approval');
    });
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>