<form action="" method="get">
    <input id="x" type="text" name="x" placeholder="0" value="<?= htmlspecialchars($x ?? '') ?>">
    <button data-operation="+" class="calcButton" type="button">+</button>
    <button data-operation="-" class="calcButton" type="button">-</button>
    <button data-operation="*" class="calcButton" type="button">*</button>
    <button data-operation="/" class="calcButton" type="button">/</button>



    <input id="y" type="text" name="y" placeholder="0" value="<?= htmlspecialchars($y ?? '') ?>">

    <button type="button">=</button>
    <input class="result" type="text" readonly id="result" value="<?= htmlspecialchars($result ?? '') ?>">
    <br>

    <button class="clear" type="button">Очистить историю</button>

    <div class="error" id="error"></div>

    <?php if ($hasCalculationAttempt && $error): ?>
        <?php foreach ($error as $err): ?>
            <div class="error"><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="result" id="textResult"><?= htmlspecialchars($textResult) ?></div>
    <?php endif; ?>


</form>

<p><a href="/">← На главную</a></p>
<script>
    window.onload = function() {
        document.querySelectorAll('.calcButton').forEach(button => {
            button.onclick = function() {
                const x = document.getElementById('x').value;
                const y = document.getElementById('y').value;
                const operation = this.getAttribute('data-operation');
                (
                    async () => {
                        try {
                            const response = await fetch(`?page=calculator&x=${x}&operation=${encodeURIComponent(operation)}&y=${y}&ajax`);
                            const result = await response.json();
                            switch (result.status) {
                                case 'success':
                                    document.getElementById('result').value = result.data.result;
                                    document.getElementById('error').innerText = '';
                                    const textResult = document.getElementById('textResult')
                                    const infoResult = document.createElement('p')
                                    infoResult.innerText = result.data.textResult;
                                    textResult.insertBefore(infoResult, textResult.firstChild);
                                    break;
                                case 'error':
                                    document.getElementById('result').value = 'Ошибка';
                                    const error = document.getElementById('error')
                                    const infoError = document.createElement('p')
                                    infoError.innerText = result.error;
                                    error.insertBefore(infoError, error.firstChild);
                                    break;
                                default:
                                    console.error('Ошибка: неверный формат ответа');
                            }

                        } catch (error) {
                            console.log('Ошибка:', error)
                        }
                    }
                )()
            }

        })
        document.querySelector('.clear').addEventListener('click', function() {
            const textResultContainer = document.getElementById('textResult');
            const errorContainer = document.getElementById('error');
            textResultContainer.innerHTML = '';
            errorContainer.innerHTML = '';

            document.getElementById('result').value = '';
        });
    }
</script>