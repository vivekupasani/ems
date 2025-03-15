// validation.js

class FormValidator {
    constructor(formId, validationRules) {
        this.form = document.getElementById(formId);
        this.rules = validationRules;
        this.errors = new Map();
        this.toastShown = new Map();
        
        this.initialize();
    }

    initialize() {
        if (!this.form) {
            console.error(`Form with ID '${formId}' not found`);
            return;
        }

        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', (e) => this.validateField(e.target));
            input.addEventListener('input', (e) => this.validateField(e.target));
        });

        this.form.addEventListener('submit', (e) => {
            this.validateAllFields();
            if (this.errors.size > 0) {
                e.preventDefault();
                this.showAllErrors();
                toast.error('Please correct the errors in the form');
            } else {
                toast.success('Form submitted successfully');
            }
        });
    }

    validateField(input) {
        const fieldName = input.name;
        const value = input.value.trim();
        const rules = this.rules[fieldName];

        if (!rules) return;

        const hadError = this.errors.has(fieldName);
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
        else if (rules.checkRepetitive && value && /^(\d)\1+$/.test(value)) {
            errorMessage = rules.repetitiveMessage || 'Invalid repetitive number';
        }
        else if (rules.minLength && value && value.length < rules.minLength) {
            errorMessage = rules.minLengthMessage || `Must be at least ${rules.minLength} characters`;
        }

        if (errorMessage) {
            this.errors.set(fieldName, errorMessage);
            this.showError(input, errorMessage);
            
            if (!hadError && !this.toastShown.get(fieldName)) {
                toast.error(`${this.getFieldLabel(input)}: ${errorMessage}`);
                this.toastShown.set(fieldName, true);
            }
        } else {
            this.errors.delete(fieldName);
            if (hadError) {
                this.toastShown.delete(fieldName);
            }
        }
    }

    getFieldLabel(input) {
        const label = input.parentElement.querySelector('label');
        return label ? label.textContent.replace(/\s*\*\s*$/, '') : 
               fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    validateAllFields() {
        const inputs = this.form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => this.validateField(input));
    }

    showError(input, message) {
        input.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        
        let errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-message')) {
            errorSpan.textContent = message;
            return;
        }

        errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.classList.add('text-red-600', 'text-xs', 'mt-1', 'block', 'font-medium');
        errorSpan.textContent = message;
        input.parentNode.insertBefore(errorSpan, input.nextSibling);
    }

    removeError(input) {
        input.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        const errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-message')) {
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
    emp_code: {
        required: true,
        pattern: /^[A-Za-z0-9-]{3,20}$/,
        errorMessage: 'Employee code must be 3-20 alphanumeric characters or hyphens'
    },
    email: {
        required: true,
        pattern: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        errorMessage: 'Invalid email format',
        minLength: 5,
        minLengthMessage: 'Email must be at least 5 characters'
    },
    mobile_number: {
        required: true,
        pattern: /^[6-9][0-9]{9}$/,
        errorMessage: 'Must be a valid 10-digit Indian mobile number starting with 6-9',
        checkRepetitive: true,
        repetitiveMessage: 'Mobile number cannot be all identical digits'
    },
    alt_number: {
        pattern: /^[6-9][0-9]{9}$/,
        errorMessage: 'Must be a valid 10-digit Indian mobile number starting with 6-9',
        checkRepetitive: true,
        repetitiveMessage: 'Alternative number cannot be all identical digits'
    },
    pan_number: {
        required: true,
        pattern: /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/,
        errorMessage: 'Invalid PAN format (e.g., ABCDE1234F)',
        checkRepetitive: true,
        repetitiveMessage: 'PAN number cannot be all identical characters'
    },
    aadhar_number: {
        required: true,
        pattern: /^[2-9][0-9]{11}$/,
        errorMessage: 'Must be a valid 12-digit Aadhar number not starting with 0 or 1',
        checkRepetitive: true,
        repetitiveMessage: 'Aadhar number cannot be all identical digits'
    },
    full_name: {
        required: true,
        pattern: /^[A-Za-z\s.]{2,50}$/,
        errorMessage: 'Full name must be 2-50 characters (letters, spaces, dots only)',
        minLength: 2
    },
    institute_name: {
        required: true,
        pattern: /^[A-Za-z\s&.,-]{2,100}$/,
        errorMessage: 'Institute name must be 2-100 characters (letters, spaces, &.,- only)',
        minLength: 2
    },
    department: {
        required: true,
        pattern: /^[A-Za-z\s-]{2,50}$/,
        errorMessage: 'Department must be 2-50 characters (letters, spaces, hyphen only)',
        minLength: 2
    },
    designation: {
        required: true,
        pattern: /^[A-Za-z\s-]{2,50}$/,
        errorMessage: 'Designation must be 2-50 characters (letters, spaces, hyphen only)',
        minLength: 2
    },
    location: {
        required: true,
        errorMessage: 'Please select a location'
    },
    joining_date: {
        required: true,
        custom: (value) => new Date(value) <= new Date(),
        errorMessage: 'Joining date cannot be in the future'
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
        custom: (value) => {
            const dob = new Date(value);
            const today = new Date();
            const age = today.getFullYear() - dob.getFullYear();
            return dob < today && age >= 18 && age <= 100;
        },
        errorMessage: 'Must be between 18 and 100 years old'
    },
    father_name: {
        required: true,
        pattern: /^[A-Za-z\s.]{2,50}$/,
        errorMessage: 'Father\'s name must be 2-50 characters (letters, spaces, dots only)',
        minLength: 2
    },
    mother_name: {
        required: true,
        pattern: /^[A-Za-z\s.]{2,50}$/,
        errorMessage: 'Mother\'s name must be 2-50 characters (letters, spaces, dots only)',
        minLength: 2
    },
    address: {
        required: true,
        minLength: 10,
        errorMessage: 'Address must be at least 10 characters'
    },
    bank_name: {
        required: true,
        errorMessage: 'Please select bank name'
    },
    branch_name: {
        required: true,
        pattern: /^[A-Za-z\s-]{2,50}$/,
        errorMessage: 'Branch name must be 2-50 characters (letters, spaces, hyphen only)',
        minLength: 2
    },
    account_number: {
        required: true,
        pattern: /^[0-9]{9,18}$/,
        errorMessage: 'Account number must be 9-18 digits',
        checkRepetitive: true,
        repetitiveMessage: 'Account number cannot be all identical digits'
    },
    ifsc_code: {
        required: true,
        pattern: /^[A-Z]{4}[0][A-Z0-9]{6}$/,
        errorMessage: 'Invalid IFSC code format (e.g., SBIN0001234)'
    },
    salary_category: {
        required: true,
        errorMessage: 'Please select salary category'
    },
    duty_hours: {
        required: true,
        custom: (value) => value > 0 && value <= 24,
        errorMessage: 'Duty hours must be between 0 and 24'
    },
    total_hours: {
        required: true,
        custom: (value) => value > 0 && value <= 168,
        errorMessage: 'Total hours must be between 0 and 168'
    },
    hours_per_day: {
        required: true,
        custom: (value) => value > 0 && value <= 24,
        errorMessage: 'Hours per day must be between 0 and 24'
    },
    salary_pay_band: {
        required: true,
        pattern: /^[A-Za-z0-9\s-]{2,20}$/,
        errorMessage: 'Salary pay band must be 2-20 characters',
        minLength: 2
    },
    basic_salary: {
        required: true,
        custom: (value) => value > 0 && value <= 10000000,
        errorMessage: 'Basic salary must be between 0 and 10,000,000'
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const validator = new FormValidator('employeeForm', employeeFormRules);
});