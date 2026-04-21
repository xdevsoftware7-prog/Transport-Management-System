<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez OBTRANS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f5f7;
            color: #111111;
            font-size: 14px;
            line-height: 1.6;
            -webkit-text-size-adjust: 100%;
        }

        .wrapper {
            width: 100%;
            padding: 40px 16px;
            background: #f4f5f7;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        /* ── HEADER ── */
        .header {
            background: #111111;
            padding: 36px 40px;
            text-align: center;
        }

        .logo-mark {
            display: inline-block;
            width: 52px;
            height: 52px;
            background: #e02020;
            border-radius: 12px;
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            line-height: 52px;
            margin-bottom: 14px;
            letter-spacing: -1px;
        }

        .brand-name {
            display: block;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .brand-sub {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1.8px;
        }

        /* Accent bar */
        .accent {
            height: 4px;
            background: linear-gradient(90deg, #e02020, #ff5555, #e02020);
        }

        /* ── HERO BANDE ── */
        .hero-band {
            background: #fafafa;
            border-bottom: 1px solid #eeeeee;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .hero-band-icon {
            width: 40px;
            height: 40px;
            background: rgba(224, 32, 32, .08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .hero-band-text {
            font-size: 15px;
            font-weight: 700;
            color: #111;
        }

        .hero-band-sub {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

        /* ── CORPS ── */
        .body {
            padding: 36px 40px 32px;
        }

        .greeting {
            font-size: 24px;
            font-weight: 900;
            color: #111111;
            margin-bottom: 16px;
            letter-spacing: -0.4px;
        }

        .body p {
            font-size: 14px;
            color: #555555;
            line-height: 1.7;
            margin-bottom: 12px;
        }

        /* ── CARTE IDENTIFIANTS ── */
        .creds-card {
            margin: 24px 0;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .creds-header {
            background: #111111;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .creds-header-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e02020;
            flex-shrink: 0;
        }

        .creds-header-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
        }

        .creds-body {
            background: #fafafa;
        }

        .cred-row {
            display: flex;
            align-items: stretch;
            border-bottom: 1px solid #eeeeee;
        }

        .cred-row:last-child {
            border-bottom: none;
        }

        .cred-label-col {
            width: 100px;
            padding: 14px 16px;
            background: #f2f2f2;
            border-right: 1px solid #eeeeee;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .cred-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #999999;
        }

        .cred-value-col {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            flex: 1;
        }

        .cred-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: 700;
            color: #111111;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 5px 12px;
            word-break: break-all;
        }

        .cred-value.pw {
            color: #e02020;
            border-color: rgba(224, 32, 32, .2);
            background: rgba(224, 32, 32, .03);
            letter-spacing: 1px;
        }

        /* ── ALERTE ── */
        .alert {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 20px 0;
        }

        .alert-title {
            font-size: 12px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 4px;
        }

        .alert p {
            font-size: 12px;
            color: #78350f;
            margin: 0;
            line-height: 1.5;
        }

        /* ── ÉTAPES ── */
        .steps {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .step-num {
            width: 24px;
            height: 24px;
            background: #111111;
            color: #ffffff;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-text {
            font-size: 13px;
            color: #444;
            padding-top: 3px;
            line-height: 1.5;
        }

        .step-text strong {
            color: #111;
        }

        /* ── CTA ── */
        .cta-wrap {
            text-align: center;
            margin: 28px 0;
        }

        .cta-btn {
            display: inline-block;
            background: #111111;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 800;
            padding: 14px 44px;
            border-radius: 8px;
            letter-spacing: 0.2px;
            text-decoration: none !important;
        }

        .cta-sub {
            font-size: 11px;
            color: #aaa;
            margin-top: 8px;
            text-align: center;
        }

        /* ── DIVIDER ── */
        .div {
            height: 1px;
            background: #f0f0f0;
            margin: 24px 0;
        }

        /* ── SIGNATURE ── */
        .sig {
            font-size: 13px;
            color: #111;
            font-weight: 600;
        }

        .sig span {
            color: #888;
            font-weight: 400;
        }

        /* ── FOOTER ── */
        .footer {
            background: #f9f9f9;
            border-top: 1px solid #eeeeee;
            padding: 24px 40px;
            text-align: center;
        }

        .footer-logo {
            font-size: 14px;
            font-weight: 800;
            color: #111;
            margin-bottom: 6px;
        }

        .footer-accent {
            width: 36px;
            height: 3px;
            background: #e02020;
            border-radius: 2px;
            margin: 8px auto;
        }

        .footer-text {
            font-size: 11px;
            color: #aaa;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .footer-legal {
            font-size: 10px;
            color: #cccccc;
        }

        /* Responsive */
        @media (max-width: 600px) {

            .body,
            .header,
            .footer {
                padding: 24px 20px;
            }

            .hero-band {
                padding: 16px 20px;
            }

            .greeting {
                font-size: 20px;
            }

            .cta-btn {
                padding: 13px 28px;
                font-size: 14px;
            }

            .cred-label-col {
                width: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">

            {{-- ── HEADER ── --}}
            <div class="header">
                <div class="logo-mark">OBS</div>
                <span class="brand-name">OBSTRANS</span>
                <span class="brand-sub">Transport Management System</span>
            </div>
            <div class="accent"></div>

            {{-- ── HERO BAND ── --}}
            <div class="hero-band">
                <div class="hero-band-icon">&#x1F44B;</div>
                <div>
                    <div class="hero-band-text">Compte créé avec succès</div>
                    <div class="hero-band-sub">Vous avez maintenant accès au portail OBTRANS TMS</div>
                </div>
            </div>

            {{-- ── CORPS ── --}}
            <div class="body">

                <div class="greeting">Bonjour {{ $userName }}&nbsp;!</div>

                <p>
                    Votre compte utilisateur a été créé par l'administrateur du système.
                    Vous pouvez désormais accéder au portail de gestion du transport OBTRANS.
                </p>

                {{-- ── CARTE IDENTIFIANTS ── --}}
                <div class="creds-card">
                    <div class="creds-header">
                        <div class="creds-header-dot"></div>
                        <span class="creds-header-title">Vos identifiants de connexion</span>
                    </div>
                    <div class="creds-body">
                        <div class="cred-row">
                            <div class="cred-label-col">
                                <span class="cred-label">Email</span>
                            </div>
                            <div class="cred-value-col">
                                <span class="cred-value">{{ $email }}</span>
                            </div>
                        </div>
                        <div class="cred-row">
                            <div class="cred-label-col">
                                <span class="cred-label">Mot de passe</span>
                            </div>
                            <div class="cred-value-col">
                                <span class="cred-value pw">{{ $password }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── ALERTE SÉCURITÉ ── --}}
                <div class="alert">
                    <div class="alert-title">&#9888;&#65039;&nbsp; Sécurité de votre compte</div>
                    <p>Ce mot de passe est provisoire. Modifiez-le dès votre première connexion depuis votre profil.</p>
                </div>


                {{-- ── CTA ── --}}
                <div class="cta-wrap">
                    <a href="{{ $loginUrl }}" class="cta-btn">
                        Se connecter au portail &rarr;
                    </a>
                    <div class="cta-sub">{{ $loginUrl }}</div>
                </div>

                <div class="div"></div>

                {{-- Signature --}}
                <div class="sig">
                    Cordialement,<br>
                    <span>L'équipe Informatique OBTRANS</span>
                </div>

            </div>{{-- /body --}}

            {{-- ── FOOTER ── --}}
            <div class="footer">
                <div class="footer-logo">OBTRANS</div>
                <div class="footer-accent"></div>
                <div class="footer-text">
                    Cet email a été envoyé automatiquement par le système TMS.<br>
                    Merci de ne pas répondre à ce message.
                </div>
                <div class="footer-legal">
                    &copy; {{ date('Y') }} OBTRANS &mdash; Tous droits réservés.
                </div>
            </div>

        </div>{{-- /container --}}
    </div>{{-- /wrapper --}}
</body>

</html>
