<?php

return [
    // Email verification
    'verify_email_heading' => 'Vérifie ton adresse',
    'verify_email_subtitle' => 'Un mail avec un code à 6 chiffres a été envoyé à :email pour confirmer ton inscription.',
    'verify_email_input_aria' => 'Code de vérification à 6 chiffres',
    'verify_email_submit' => 'Vérifier',
    'verify_email_submitting' => 'Vérification…',
    'verify_email_resend' => 'Renvoyer un code',
    'verify_email_resend_throttled' => 'Patiente :seconds secondes avant de renvoyer.',
    'verify_email_resend_sent' => 'Un nouveau code vient de partir.',
    'verify_email_invalid' => 'Code incorrect ou expiré.',
    'verify_email_throttled' => 'Trop de tentatives. Réessaie dans :seconds secondes.',
    'verify_email_subject' => 'Bienvenue sur Giggr. — Confirme ton adresse',
    'verify_email_greeting' => 'Bonjour :name,',
    'verify_email_intro' => 'Voici ton code de vérification :',
    'verify_email_expiry' => 'Ce code est valable 10 minutes.',
    'verify_email_expires_at' => 'Code valable jusqu\'à :time.',
    'verify_email_ignore' => "Si tu n'as pas créé de compte sur Giggr, ignore simplement ce mail.",
    'verify_email_salutation_html' => "Merci,<br>L'équipe Giggr.",
    'verify_email_already_verified' => 'Ton adresse est déjà vérifiée.',

    // Laravel defaults
    'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
    'email_taken' => 'Cette adresse e-mail est déjà associée à un compte. <a href=":url" class="underline font-medium">Se connecter ?</a>',
    'password' => 'Le mot de passe fourni est incorrect.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

    // Layout left panel
    'panel_eyebrow' => 'Rejoins la scène',
    'panel_subtitle' => 'Musiciens et organisateurs, trouvez-vous ici.',

    // Shared
    'email_label' => 'Adresse e-mail',
    'email_placeholder' => 'marie@exemple.com',
    'password_label' => 'Mot de passe',
    'password_show' => 'Afficher le mot de passe',
    'password_hide' => 'Masquer le mot de passe',
    'back_to_login' => '← Retour à la connexion',
    'required_legend' => "Les champs marqués d'un * sont obligatoires.",

    // Register
    'register_heading' => 'Créer un compte',
    'register_subtitle' => "Quelques infos, et c'est parti.",
    'register_first_name' => 'Prénom',
    'register_first_name_ph' => 'Marie',
    'register_last_name' => 'Nom',
    'register_last_name_ph' => 'Dupont',
    'register_password_ph' => '8 caractères minimum',
    'register_submit' => 'Rejoindre Giggr.',
    'register_login_prompt' => 'Déjà un compte ?',
    'register_login_link' => 'Se connecter',

    // Login
    'login_heading' => 'Se connecter',
    'login_subtitle' => 'Content de te revoir.',
    'login_forgot' => 'Mot de passe oublié ?',
    'login_submit' => 'Se connecter',
    'login_register_prompt' => 'Pas encore de compte ?',
    'login_register_link' => 'Créer un compte',

    // Forgot password
    'forgot_heading' => 'Mot de passe oublié ?',
    'forgot_subtitle' => "Saisis ton adresse e-mail, on t'envoie un lien de réinitialisation.",
    'forgot_submit' => 'Envoyer le lien',
    'forgot_sent_heading' => 'Vérifie ta boîte mail',
    'forgot_sent_subtitle' => 'Si cette adresse est associée à un compte, tu recevras un e-mail dans quelques minutes.',
    'forgot_sent_spam' => 'Pense à vérifier ton dossier spam si tu ne vois rien arriver.',
    'forgot_sent_back' => 'Retour à la connexion',

    // Reset password
    'reset_heading' => 'Nouveau mot de passe',
    'reset_subtitle' => 'Choisis un mot de passe sécurisé pour ton compte.',
    'reset_new_password' => 'Nouveau mot de passe',
    'reset_new_password_ph' => '8 caractères minimum',
    'reset_confirm_password' => 'Confirmer le mot de passe',
    'reset_confirm_password_ph' => 'Répète ton mot de passe',
    'reset_submit' => 'Réinitialiser le mot de passe',

];
