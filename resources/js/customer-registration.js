const registrationForm = document.querySelector('#registerForm');

if (registrationForm) {
    const form = registrationForm;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const endpoints = {
        saveLead: form.dataset.saveLeadUrl,
        sendOtp: form.dataset.sendOtpUrl,
        verifyOtp: form.dataset.verifyOtpUrl,
        createIncomplete: form.dataset.createIncompleteUrl,
        complete: form.dataset.completeUrl,
    };
    const infoStep = document.querySelector('#personalInfoSection');
    const verificationStep = document.querySelector('#verifyPasswordSection');
    const otpSection = document.querySelector('#otpVerificationSection');
    const passwordSection = document.querySelector('#passwordInputSection');
    const continueButton = document.querySelector('#continueBtn');
    const verifyButton = document.querySelector('#verifyOtpBtn');
    const resendButton = document.querySelector('#resendOtp');
    const completeButton = document.querySelector('#registerButton');
    const otpInputs = [...document.querySelectorAll('[data-otp-digit]')];
    const otpValue = document.querySelector('#fullOtp');
    const otpTimerLabel = document.querySelector('#otpTimer');
    const backButton = document.querySelector('#backToInfoBtn');
    const termsCheckbox = document.querySelector('#terms_accepted');
    const termsError = document.querySelector('#terms-error');
    let currentUserId = null;
    let countdownTimer = null;
    let countdown = 600;
    let requestInProgress = false;

    const getField = (name) => form.elements.namedItem(name);
    const getValues = () => ({
        first_name: getField('first_name').value.trim(),
        last_name: getField('last_name').value.trim(),
        phone: getField('phone').value.trim(),
        email: getField('email').value.trim(),
    });

    const showToast = (type, message) => window.YuktioneToast[type](message);

    const setFieldError = (name, message) => {
        const field = getField(name);
        const error = document.querySelector(`#${name}-error`);
        if (!field || !error) {
            return;
        }

        field.setAttribute('aria-invalid', message ? 'true' : 'false');
        error.textContent = message || '';
        error.classList.toggle('hidden', !message);
    };

    const clearFieldErrors = () => {
        ['first_name', 'last_name', 'phone', 'email', 'otp', 'password', 'password_confirmation'].forEach((name) => {
            setFieldError(name, '');
        });
        termsError.classList.add('hidden');
    };

    const applyFieldErrors = (errors = {}) => {
        Object.entries(errors).forEach(([name, messages]) => {
            if (['first_name', 'last_name', 'phone', 'email', 'otp', 'password', 'password_confirmation'].includes(name)) {
                setFieldError(name, Array.isArray(messages) ? messages[0] : messages);
            }
        });
    };

    const request = async (url, values, fallbackMessage) => {
        let response;
        try {
            response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new URLSearchParams({ ...values, _token: csrfToken }),
            });
        } catch {
            throw new Error(fallbackMessage);
        }

        let result;
        try {
            result = await response.json();
        } catch {
            throw new Error(fallbackMessage);
        }

        if (!response.ok || result.status !== 'success') {
            const error = new Error(response.status >= 500
                ? fallbackMessage
                : (result.message || fallbackMessage));
            error.validationErrors = result.errors || {};
            error.errorType = result.error_type;
            throw error;
        }

        return result;
    };

    const setLoading = (button, loading, idleText, loadingText) => {
        button.disabled = loading;
        button.textContent = loading ? loadingText : idleText;
    };

    const setStep = (activeStep) => {
        infoStep.hidden = activeStep !== 1;
        verificationStep.hidden = activeStep !== 2;

        const first = document.querySelector('#stepInfo');
        const second = document.querySelector('#stepVerifyPassword');
        const connector = document.querySelector('#stepConnector');
        const setIndicator = (item, state) => {
            const indicator = item.querySelector('.step-indicator');
            const label = item.querySelector('.step-label');
            const isActive = state === 'active';
            const isComplete = state === 'complete';

            indicator.className = `step-indicator grid size-7 place-items-center rounded-full border-2 text-xs font-semibold ${
                isActive
                    ? 'border-brand-primary bg-brand-primary text-white'
                    : isComplete
                        ? 'border-success bg-success text-white'
                        : 'border-border bg-white text-text-muted'
            }`;
            indicator.textContent = isComplete ? '✓' : (item === first ? '1' : '2');
            label.className = `step-label text-xs ${
                isActive
                    ? 'font-semibold text-brand-primary'
                    : isComplete
                        ? 'font-semibold text-success'
                        : 'font-medium text-text-muted'
            }`;
        };
        setIndicator(first, activeStep === 1 ? 'active' : 'complete');
        setIndicator(second, activeStep === 2 ? 'active' : 'idle');
        first.setAttribute('aria-current', activeStep === 1 ? 'step' : 'false');
        second.setAttribute('aria-current', activeStep === 2 ? 'step' : 'false');
        connector.classList.toggle('bg-brand-primary', activeStep === 2);
        connector.classList.toggle('bg-brand-100', activeStep !== 2);
    };

    const validatePersonalInformation = () => {
        clearFieldErrors();
        const values = getValues();
        let valid = true;

        if (!values.first_name) {
            setFieldError('first_name', 'Enter your first name.');
            valid = false;
        }
        if (!values.last_name) {
            setFieldError('last_name', 'Enter your last name.');
            valid = false;
        }
        if (values.phone.length < 10 || values.phone.length > 15) {
            setFieldError('phone', 'Enter a phone number between 10 and 15 characters.');
            valid = false;
        }
        if (!values.email || !getField('email').validity.valid) {
            setFieldError('email', 'Enter a valid email address.');
            valid = false;
        }

        if (!valid) {
            showToast('error', 'Please complete the required fields.');
            form.querySelector('[aria-invalid="true"]')?.focus();
        }
        return valid;
    };

    const updateOtpValue = () => {
        otpValue.value = otpInputs.map((input) => input.value).join('');
        setFieldError('otp', '');
    };

    const startTimer = () => {
        window.clearInterval(countdownTimer);
        countdown = 600;
        const updateDisplay = () => {
            const minutes = Math.floor(countdown / 60);
            const seconds = String(countdown % 60).padStart(2, '0');
            otpTimerLabel.textContent = countdown > 0
                ? `Code expires in ${minutes}:${seconds}`
                : 'Code expired. Request a new one.';
        };

        updateDisplay();
        countdownTimer = window.setInterval(() => {
            countdown -= 1;
            updateDisplay();
            if (countdown <= 0) {
                window.clearInterval(countdownTimer);
            }
        }, 1000);
    };

    const beginVerification = async () => {
        if (requestInProgress || !validatePersonalInformation()) {
            return;
        }

        requestInProgress = true;
        setLoading(continueButton, true, 'Continue', 'Sending code…');
        const values = getValues();

        try {
            await request(endpoints.saveLead, values, 'Unable to save your information. Please try again.');
            await request(endpoints.sendOtp, { email: values.email }, 'Unable to send a verification code. Please try again.');
            document.querySelector('#verifiedEmail').textContent = values.email;
            otpInputs.forEach((input) => { input.value = ''; });
            updateOtpValue();
            setStep(2);
            startTimer();
            showToast('success', 'Verification code sent successfully.');
        } catch (error) {
            applyFieldErrors(error.validationErrors);
            showToast('error', error.message || 'Unable to send a verification code. Please try again.');
            form.querySelector('[aria-invalid="true"]')?.focus();
        } finally {
            requestInProgress = false;
            setLoading(continueButton, false, 'Continue', 'Sending code…');
        }
    };

    const resendOtp = async (event) => {
        event.preventDefault();
        if (requestInProgress) {
            return;
        }

        requestInProgress = true;
        setLoading(resendButton, true, 'Resend code', 'Sending…');
        try {
            await request(endpoints.sendOtp, { email: getValues().email }, 'Unable to resend the verification code. Please try again.');
            otpInputs.forEach((input) => { input.value = ''; });
            updateOtpValue();
            startTimer();
            showToast('success', 'Verification code sent successfully.');
            otpInputs[0].focus();
        } catch (error) {
            showToast('error', error.message || 'Unable to resend the verification code. Please try again.');
        } finally {
            requestInProgress = false;
            setLoading(resendButton, false, 'Resend code', 'Sending…');
        }
    };

    const createIncompleteUser = async () => {
        const result = await request(endpoints.createIncomplete, {
            ...getValues(),
            otp: otpValue.value,
        }, 'Unable to verify your email. Please try again.');

        currentUserId = result.user_id;
        window.clearInterval(countdownTimer);
        otpSection.hidden = true;
        passwordSection.hidden = false;
        backButton.hidden = true;
        showToast('success', result.message || 'Email verified successfully.');
        getField('password').focus();
    };

    const verifyOtp = async () => {
        if (requestInProgress) {
            return;
        }
        clearFieldErrors();

        if (!/^\d{6}$/.test(otpValue.value)) {
            setFieldError('otp', 'Enter the complete 6-digit verification code.');
            showToast('error', 'Enter the complete 6-digit verification code.');
            otpInputs[0].focus();
            return;
        }

        requestInProgress = true;
        setLoading(verifyButton, true, 'Verify email', 'Verifying…');
        try {
            await request(endpoints.verifyOtp, {
                email: getValues().email,
                otp: otpValue.value,
            }, 'Invalid or expired verification code.');
            await createIncompleteUser();
        } catch (error) {
            applyFieldErrors(error.validationErrors);
            showToast('error', error.message || 'Invalid or expired verification code.');
            if (error.errorType === 'expired' || error.errorType === 'already_verified') {
                otpInputs.forEach((input) => { input.value = ''; });
                updateOtpValue();
                if (error.errorType === 'already_verified') {
                    window.setTimeout(() => resendButton.click(), 500);
                }
            }
        } finally {
            requestInProgress = false;
            setLoading(verifyButton, false, 'Verify email', 'Verifying…');
        }
    };

    const completeRegistration = async (event) => {
        event.preventDefault();
        if (requestInProgress) {
            return;
        }
        clearFieldErrors();

        if (!currentUserId) {
            showToast('error', 'Verify your email before creating your password.');
            return;
        }

        const password = getField('password').value;
        const confirmation = getField('password_confirmation').value;
        let valid = true;
        if (password.length < 6) {
            setFieldError('password', 'Use at least 6 characters for your password.');
            valid = false;
        }
        if (!confirmation) {
            setFieldError('password_confirmation', 'Confirm your password.');
            valid = false;
        } else if (password !== confirmation) {
            setFieldError('password_confirmation', 'Passwords do not match.');
            valid = false;
        }
        if (!termsCheckbox.checked) {
            termsError.classList.remove('hidden');
            valid = false;
        }
        if (!valid) {
            showToast('error', 'Check the highlighted fields before continuing.');
            form.querySelector('[aria-invalid="true"]')?.focus();
            return;
        }

        requestInProgress = true;
        setLoading(completeButton, true, 'Create account', 'Creating account…');
        try {
            const result = await request(endpoints.complete, {
                user_id: currentUserId,
                password,
                password_confirmation: confirmation,
                terms_accepted: termsCheckbox.checked ? '1' : '0',
            }, 'Unable to create your account. Please try again.');
            if (result.registered_email) {
                try {
                    localStorage.setItem('registered_email', result.registered_email);
                } catch (error) {
                    console.warn('Could not save the registration email for sign-in.', error);
                }
            }
            showToast('success', result.message || 'Your account has been created successfully.');
            window.setTimeout(() => {
                window.location.assign(result.redirect_url);
            }, 900);
        } catch (error) {
            applyFieldErrors(error.validationErrors);
            showToast('error', error.message || 'Unable to create your account. Please try again.');
        } finally {
            requestInProgress = false;
            setLoading(completeButton, false, 'Create account', 'Creating account…');
        }
    };

    continueButton.addEventListener('click', beginVerification);
    verifyButton.addEventListener('click', verifyOtp);
    resendButton.addEventListener('click', resendOtp);
    form.addEventListener('submit', (event) => {
        if (!infoStep.hidden) {
            event.preventDefault();
            beginVerification();
            return;
        }
        completeRegistration(event);
    });
    backButton.addEventListener('click', () => {
        if (currentUserId) {
            return;
        }
        window.clearInterval(countdownTimer);
        clearFieldErrors();
        setStep(1);
        getField('first_name').focus();
    });

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(-1);
            updateOtpValue();
            if (input.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
        input.addEventListener('paste', (event) => {
            const digits = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            if (!digits) {
                return;
            }
            event.preventDefault();
            otpInputs.forEach((digitInput, digitIndex) => {
                digitInput.value = digits[digitIndex] || '';
            });
            updateOtpValue();
            otpInputs[Math.min(digits.length, otpInputs.length) - 1]?.focus();
        });
    });

    form.querySelectorAll('input:not([type="checkbox"]):not([type="hidden"])').forEach((field) => {
        field.addEventListener('input', () => setFieldError(field.name, ''));
    });
    termsCheckbox.addEventListener('change', () => termsError.classList.add('hidden'));

    form.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const field = getField(button.dataset.togglePassword);
            const visible = field.type === 'password';
            field.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
        });
    });

    document.querySelector('#openTerms').addEventListener('click', () => {
        document.querySelector('#termsDialog').showModal();
    });
    document.querySelectorAll('[data-close-terms]').forEach((button) => {
        button.addEventListener('click', () => document.querySelector('#termsDialog').close());
    });
    document.querySelector('#termsDialog').addEventListener('click', (event) => {
        if (event.target === event.currentTarget) {
            event.currentTarget.close();
        }
    });
}
