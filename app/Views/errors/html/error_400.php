<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Errors.badRequest') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: "Inter", system-ui, sans-serif;
            background: #f9fafb;
            color: #111827;
            -webkit-font-smoothing: antialiased;
        }

        .wrap {
            max-width: 28rem;
            width: 100%;
            text-align: center;
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 1.25rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }

        .code {
            font-family: "Outfit", system-ui, sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1;
            color: #0185c6;
            margin: 0 0 0.5rem;
            letter-spacing: -0.02em;
        }

        h1 {
            font-family: "Outfit", system-ui, sans-serif;
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0 0 0.5rem;
            color: #111827;
        }

        p {
            margin: 0;
            font-size: 0.9375rem;
            color: #6b7280;
            line-height: 1.5;
        }

        a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: #0185c6;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background 0.2s, box-shadow 0.2s;
        }

        a:hover {
            background: #0170a8;
            box-shadow: 0 4px 6px -1px rgb(1 133 198 / 0.3);
        }
    </style>
</head>

<body>
    <div class="wrap">
        <p class="code">400</p>
        <h1>Requisição inválida</h1>
        <p>
            <?php if (ENVIRONMENT !== 'production'): ?>
                <?= nl2br(esc($message)) ?>
            <?php else: ?>
                <?= lang('Errors.sorryBadRequest') ?>
            <?php endif; ?>
        </p>
        <a href="/">Voltar ao início</a>
    </div>
</body>

</html>