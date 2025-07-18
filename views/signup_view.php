<?php

function display_signup_errors(array $errors): void {
    if (!empty($errors)): ?>
        <div class="error-messages" style="
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(100% - 20px);
            margin-left: auto;
            margin-right: auto;
            display: block;
            visibility: visible;
            opacity: 1;">

            <ul style="
                list-style-type: none;
                padding: 0;
                margin: 0;">

                <?php foreach ($errors as $error): ?>
                    <li style="
                        font-weight: bold;
                        padding: 8px 0;
                        border-bottom: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($error); ?>
                    </li>
                <?php endforeach; ?>

            </ul>
        </div>
    <?php endif;
}
