      // Function to toggle the navigation drawer and overlay
      function toggleDrawer(show) {
        const drawer = document.getElementById('navigationDrawer');
        const overlay = document.getElementById('overlay');
    
        if (show) {
          // Show the drawer and overlay
          drawer.classList.remove('-translate-x-full');
          overlay.classList.remove('hidden', 'opacity-0');
        } else {
          // Hide the drawer and overlay
          drawer.classList.add('-translate-x-full');
          overlay.classList.add('opacity-0');
          setTimeout(() => overlay.classList.add('hidden'), 300); // Delay hiding the overlay for the animation
        }
      }

          // Close drawer when clicking outside
    document.addEventListener('click', function(event) {
      const drawer = document.getElementById('navigationDrawer');
      const menuButton = document.querySelector('.fixed.top-4.left-4 button');
      
      if (!drawer.contains(event.target) && !menuButton.contains(event.target)) {
        toggleDrawer(false);
      }
    });

    document.addEventListener('DOMContentLoaded', function() {
      const navDiv = document.querySelector('nav .flex.items-center');
      const menuButton = document.createElement('button');
      menuButton.className = 'md:hidden mr-4';
      menuButton.onclick = () => toggleDrawer(true);
      menuButton.innerHTML = '<img src="hamburger.png" alt="Menu" class="h-6 w-6">';
      navDiv.insertBefore(menuButton, navDiv.firstChild);
    });
    
      // Function to display specific sections
      function showSection(sectionId) {
        alert(`Navigating to ${sectionId}`);
        toggleDrawer(false);
      }
        
        // Previous showSection function remains same
        function showSection(sectionId) {
          document.querySelectorAll(".section-content").forEach((section) => {
            section.classList.add("hidden");
          });
          document.getElementById(sectionId).classList.remove("hidden");
        }
    
        // New function to handle manage employee sections
        function showManageSection(sectionId) {
          // Hide all manage sections
          document.querySelectorAll(".manage-section").forEach((section) => {
            section.classList.add("hidden");
          });
    
          // Remove active state from all buttons
          document.querySelectorAll(".manage-nav-btn").forEach((btn) => {
            btn.classList.remove("text-blue-600", "border-blue-600");
            btn.classList.add("text-gray-600", "border-transparent");
          });
    
          // Show selected section
          document.getElementById(sectionId).classList.remove("hidden");
    
          // Add active state to clicked button
          event.currentTarget.classList.remove(
            "text-gray-600",
            "border-transparent"
          );
          event.currentTarget.classList.add("text-blue-600", "border-blue-600");
        }
    
        // Show dashboard and appointment form by default
        document.addEventListener("DOMContentLoaded", () => {
          showSection("dashboard");
          showManageSection("appointment-form");
        });
    
        document
          .querySelector('select[name="salaryCategory"]')
          .addEventListener("change", function(e) {
            const otherCategory = document.getElementById("otherSalaryCategory");
            if (e.target.value === "other") {
              otherCategory.classList.remove("hidden");
            } else {
              otherCategory.classList.add("hidden");
            }
          });
 
  
    
        // backend connection below
        document.addEventListener('DOMContentLoaded', function() {
          const form = document.getElementById('employeeForm');
    
          // Handle salary category change
          document.querySelector('select[name="salaryCategory"]').addEventListener('change', function(e) {
            const otherCategory = document.getElementById('otherSalaryCategory');
            if (e.target.value === 'other') {
              otherCategory.classList.remove('hidden');
            } else {
              otherCategory.classList.add('hidden');
            }
          });
    
          // Handle form submission
          form.addEventListener('submit', function(e) {
            e.preventDefault();
    
            const formData = new FormData(form);
    
            // Add mode (insert/update)
            const employeeId = new URLSearchParams(window.location.search).get('id');
            if (employeeId) {
              formData.append('mode', 'update');
              formData.append('employee_id', employeeId);
            } else {
              formData.append('mode', 'insert');
            }
    
            fetch('process.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if (data.status === 'success') {
                  alert(data.message);
                  if (!employeeId) {
                    form.reset();
                  }
                } else {
                  alert('Error: ' + data.message);
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the data');
              });
          });
    
          // Load employee data if in edit mode
          const employeeId = new URLSearchParams(window.location.search).get('id');
          if (employeeId) {
            fetch(`fetch_employee.php?id=${employeeId}`)
              .then(response => response.json())
              .then(data => {
                Object.keys(data).forEach(key => {
                  const element = form.elements[key];
                  if (element) {
                    element.value = data[key];
                  }
                });
    
                // Handle salary category
                if (data.salary_category === 'other') {
                  document.getElementById('otherSalaryCategory').classList.remove('hidden');
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('Error loading employee data');
              });
          }
        });

  // Check URL Parameters
  const urlParams = new URLSearchParams(window.location.search);
  const success = urlParams.get('success');

  // If success=true, show the 'view-employees' section
  if (success === 'true') {
      document.getElementById('view-employees').classList.remove('hidden');
  }


    function editEmployee(id) {
        window.location.href = `update-ins.php?id=${id}`;
    }

    function deleteEmployee(id) {
            var result = confirm('Are you sure you want to delete this employee?');
            if (result) {
                window.location.href = 'delete.php?id=' + id + '&confirm=true';
            }
        }

   
    // Function to export employee data to Excel
