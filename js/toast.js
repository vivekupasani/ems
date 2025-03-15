// validation.js

class FormValidator {
    constructor(formId, validationRules) {
        this.form = document.getElementById(formId);
        this.rules = validationRules;
        this.errors = new Map();
        
        this.initialize();
    }

    initialize() {
        if (!this.form) return;

        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', (e) => this.validateField(e.target));
        });

        this.form.addEventListener('submit', (e) => {
            this.validateAllFields();
            if (this.errors.size > 0) {
                e.preventDefault();
                this.showAllErrors();
                toast.error('Please correct the errors in the form');
            } else {
                // Show success toast (you might want to move this to after successful server response)
                toast.success('Form submitted successfully');
            }
        });
    }

    validateField(input) {
        const fieldName = input.name;
        const value = input.value.trim();
        const rules = this.rules[fieldName];

        if (!rules) return;

        this.removeError(input);

        let errorMessage = '';

        if (rules.required && !value) {
            errorMessage = rules.requiredMessage || 'This field is required';
        }
        else if (rules.pattern && value && !rules.pattern.test(value)) {
            errorMessage = rules.errorMessage || 'Invalid format';
        }
        else if (rules.custom && value && !rules.custom(value)) {
            errorMessage = rules.errorMessage || 'Invalid value';
        }

        if (errorMessage) {
            this.errors.set(fieldName, errorMessage);
            this.showError(input, errorMessage);
        } else {
            this.errors.delete(fieldName);
            input.classList.remove('border-red-500');
        }
    }

    validateAllFields() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => this.validateField(input));
    }

    showError(input, message) {
        input.classList.add('border-red-500');
        
        let errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.className === 'error-message') {
            errorSpan.textContent = message;
            return;
        }

        errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.classList.add('text-red-500', 'text-xs', 'mt-1', 'block');
        errorSpan.textContent = message;
        input.parentNode.insertBefore(errorSpan, input.nextSibling);
    }

    removeError(input) {
        input.classList.remove('border-red-500');
        const errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.className === 'error-message') {
            errorSpan.remove();
        }
    }

    showAllErrors() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const error = this.errors.get(input.name);
            if (error) {
                this.showError(input, error);
            }
        });
    }
}

const employeeFormRules = {
    email: {
        required: true,
        pattern: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        errorMessage: 'Invalid email format'
    },
    mobile_number: {
        required: true,
        pattern: /^[0-9]{10}$/,
        errorMessage: 'Must be a 10-digit number'
    },
    alt_number: {
        pattern: /^[0-9]{10}$/,
        errorMessage: 'Must be a 10-digit number'
    },
    pan_number: {
        required: true,
        pattern: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/,
        errorMessage: 'Invalid PAN format (e.g., ABCDE1234F)'
    },
    aadhar_number: {
        required: true,
        pattern: /^[0-9]{12}$/,
        errorMessage: 'Must be a 12-digit number'
    },
    full_name: {
        required: true,
        errorMessage: 'Full name is required'
    },
    institute_name: {
        required: true,
        errorMessage: 'Institute name is required'
    },
    department: {
        required: true,
        errorMessage: 'Department is required'
    },
    designation: {
        required: true,
        errorMessage: 'Designation is required'
    },
    location: {
        required: true,
        errorMessage: 'Please select a location'
    },
    joining_date: {
        required: true,
        errorMessage: 'Joining date is required'
    },
    emp_category: {
        required: true,
        errorMessage: 'Please select employee category'
    },
    gender: {
        required: true,
        errorMessage: 'Please select gender'
    },
    blood_group: {
        required: true,
        errorMessage: 'Please select blood group'
    },
    nationality: {
        required: true,
        errorMessage: 'Please select nationality'
    },
    dob: {
        required: true,
        errorMessage: 'Date of birth is required'
    },
    father_name: {
        required: true,
        errorMessage: 'Father\'s name is required'
    },
    mother_name: {
        required: true,
        errorMessage: 'Mother\'s name is required'
    },
    address: {
        required: true,
        errorMessage: 'Address is required'
    },
    bank_name: {
        required: true,
        errorMessage: 'Please select bank name'
    },
    branch_name: {
        required: true,
        errorMessage: 'Branch name is required'
    },
    account_number: {
        required: true,
        errorMessage: 'Account number is required'
    },
    ifsc_code: {
        required: true,
        pattern: /^[A-Z]{4}[0-9]{7}$/,
        errorMessage: 'Invalid IFSC code format (e.g., SBIN0001234)'
    },
    salary_category: {
        required: true,
        errorMessage: 'Please select salary category'
    },
    duty_hours: {
        required: true,
        errorMessage: 'Duty hours are required'
    },
    total_hours: {
        required: true,
        errorMessage: 'Total hours are required'
    },
    hours_per_day: {
        required: true,
        errorMessage: 'Hours per day are required'
    },
    salary_pay_band: {
        required: true,
        errorMessage: 'Salary pay band is required'
    },
    basic_salary: {
        required: true,
        errorMessage: 'Basic salary is required'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const validator = new FormValidator('employeeForm', employeeFormRules);
});