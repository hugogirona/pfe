<?php

return [
    // Email verification
    'verify_email_heading' => 'Verify your email',
    'verify_email_subtitle' => 'We sent a 6-digit code to :email to confirm your sign up.',
    'verify_email_input_aria' => '6-digit verification code',
    'verify_email_submit' => 'Verify',
    'verify_email_submitting' => 'Verifying…',
    'verify_email_resend' => 'Resend a code',
    'verify_email_resend_throttled' => 'Wait :seconds seconds before resending.',
    'verify_email_resend_sent' => 'A new code is on its way.',
    'verify_email_invalid' => 'Incorrect or expired code.',
    'verify_email_throttled' => 'Too many attempts. Try again in :seconds seconds.',
    'verify_email_subject' => 'Welcome to Giggr. — Confirm your email',
    'verify_email_greeting' => 'Hi :name,',
    'verify_email_intro' => 'Here is your verification code:',
    'verify_email_expiry' => 'This code is valid for 10 minutes.',
    'verify_email_expires_at' => 'Code valid until :time.',
    'verify_email_ignore' => "If you didn't request this, you can safely ignore this email.",
    'verify_email_salutation_html' => 'Thanks,<br>The Giggr team.',

    // Password reset
    'password_reset_subject' => 'Reset your password — Giggr.',
    'password_reset_greeting' => 'Hi :name,',
    'password_reset_intro' => 'You requested a password reset for your Giggr account. Click the button below to pick a new password.',
    'password_reset_button' => 'Reset my password',
    'password_reset_expiry' => 'This link expires in :minutes minutes.',
    'password_reset_ignore' => "If you didn't request this, just ignore this email — your password won't be changed.",
    'verify_email_already_verified' => 'Your email is already verified.',

    // Laravel defaults
    'failed' => 'These credentials do not match our records.',
    'email_taken' => 'This email address is already linked to an account. <a href=":url" class="underline font-medium">Log in?</a>',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Layout left panel
    'panel_eyebrow' => 'Join the scene',
    'panel_subtitle' => 'Musicians and organisers, find each other here.',

    // Shared
    'email_label' => 'Email address',
    'email_placeholder' => 'marie@example.com',
    'password_label' => 'Password',
    'password_show' => 'Show password',
    'password_hide' => 'Hide password',
    'back_to_login' => '← Back to sign in',
    'required_legend' => 'Fields marked with * are required.',

    // Register
    'register_heading' => 'Create an account',
    'register_subtitle' => 'A few details and you\'re in.',
    'register_first_name' => 'First name',
    'register_first_name_ph' => 'Marie',
    'register_last_name' => 'Last name',
    'register_last_name_ph' => 'Dupont',
    'register_birth_date' => 'Date of birth',
    'register_password_ph' => 'At least 8 characters',
    'register_submit' => 'Join Giggr.',
    'register_login_prompt' => 'Already have an account?',
    'register_login_link' => 'Sign in',

    // Login
    'login_heading' => 'Sign in',
    'login_subtitle' => 'Good to see you again.',
    'login_forgot' => 'Forgot your password?',
    'login_remember' => 'Remember me',
    'login_submit' => 'Sign in',
    'login_register_prompt' => 'No account yet?',
    'login_register_link' => 'Create an account',

    // Forgot password
    'forgot_heading' => 'Forgot your password?',
    'forgot_subtitle' => 'Enter your email address and we\'ll send you a reset link.',
    'forgot_submit' => 'Send reset link',
    'forgot_sent_heading' => 'Check your inbox',
    'forgot_sent_subtitle' => 'If this address is linked to an account, you\'ll receive an email within a few minutes.',
    'forgot_sent_spam' => 'Don\'t forget to check your spam folder if nothing arrives.',
    'forgot_sent_back' => 'Back to sign in',

    // Reset password
    'reset_heading' => 'New password',
    'reset_subtitle' => 'Choose a strong password for your account.',
    'reset_new_password' => 'New password',
    'reset_new_password_ph' => 'At least 8 characters',
    'reset_confirm_password' => 'Confirm password',
    'reset_confirm_password_ph' => 'Repeat your password',
    'reset_submit' => 'Reset password',

];
