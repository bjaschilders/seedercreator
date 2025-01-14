<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = trim($_POST["title"]);
        $seederTitle = $title . "Seeder";
        $tableTitle = trim($_POST["tabletitle"]);
        $title = strtolower(str_replace(' ', '_', $title));
        
        $fileName = $seederTitle . '.php';
        $filePath = __DIR__ . '/' . $fileName;

        $columns = '';
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'column_title_') === 0) {
                $index = str_replace('column_title_', '', $key);
                $columnTitle = $value;
                $columnType = $_POST["column_type_$index"];

                switch ($columnType) {
                    case 'firstname':
                        $columns .= "\t\t\t'$columnTitle' => \$faker->firstName(),\n";
                        break;
                    case 'lastname':
                        $columns .= "\t\t\t'$columnTitle' => \$faker->lastName(),\n";
                        break;
                    case 'timestamp':
                        $columns .= "\t\t\t\'$columnTitle' => date('Y-m-d H:i:s'),\n";
                        break;
                    case 'randnumb':
                        $randNumbAmount = $_POST["random_number_amount_$index"];
                        $columns .= "\t\t\t'$columnTitle' => \$faker->numerify('" . str_repeat('#', $randNumbAmount) . "'),\n";
                        break;
                    case 'randbetweennumb':
                        $betweenNumbAmount1 = $_POST["between_number_amount_1_$index"];
                        $betweenNumbAmount2 = $_POST["between_number_amount_2_$index"];
                        $columns .= "\t\t\t'$columnTitle' => rand($betweenNumbAmount1, $betweenNumbAmount2),\n";
                        break;
                    case 'randtext':
                        $randTextAmount = $_POST["random_text_amount_$index"];
                        $columns .= "\t\t\t'$columnTitle' => Str::random($randTextAmount),\n";
                        break;
                    case 'spectext':
                        $specTextAmount = $_POST["specific_text_amount_$index"];
                        $columns .= "\t\t\t'$columnTitle' => '$specTextAmount',\n";
                        break;
                }
            }
        }

        $fileContent = <<<EOT
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class $seederTitle extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \$faker = \Faker\Factory::create();

        DB::table('$tableTitle')->insert([
$columns\t\t]);
    }
}

EOT;

        if (file_put_contents($filePath, $fileContent) !== false) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&filename=" . urlencode($fileName));
            exit();
        } else {
            echo "<p>Error: Unable to create the file.</p>";
        }
    }

    if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['filename'])) {
        $fileName = htmlspecialchars($_GET['filename']);
        echo "<p>File <strong>$fileName</strong> created successfully.</p>";
    }
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Seeder Generator</title>
    <style>
        .dynamic-field { margin-bottom: 15px; }
    </style>
    <script>
        let fieldCount = 0;

        function addField() {
            fieldCount++;
            const container = document.getElementById('dynamic-fields');

            const fieldDiv = document.createElement('div');
            fieldDiv.classList.add('dynamic-field');
            fieldDiv.id = `field-${fieldCount}`;

            fieldDiv.innerHTML = `
                <label for="title-${fieldCount}">Column Title:</label>
                <input type="text" name="column_title_${fieldCount}" id="title-${fieldCount}" required>
                <label for="type-${fieldCount}">Type:</label>
                <select name="column_type_${fieldCount}" id="type-${fieldCount}" onchange="handleTypeChange(${fieldCount})">
                    <option value="firstname">Random First Name</option>
                    <option value="lastname">Random Last Name</option>
                    <option value="randnumb">Random Number</option>
                    <option value="randbetweennumb">Random Number Between Numbers</option>
                    <option value="randtext">Random Text</topnio>
                    <option value="specnumb">Specific Text</option>
                    <option value="timestamp">Current Time</option>
                </select>
                <div id="random-number-${fieldCount}" style="display: none; margin-top: 10px;">
                    <label for="random-number-amount-${fieldCount}">Random Number Amount:</label>
                    <input type="text" name="random_number_amount_${fieldCount}" id="random-number-amount-${fieldCount}">
                </div>
                <div id="random-text-${fieldCount}" style="display: none; margin-top: 10px;">
                    <label for="random-text-amount-${fieldCount}">Random Text Amount:</label>
                    <input type="text" name="random_text_amount_${fieldCount}" id="random-text-amount-${fieldCount}">
                </div>
                <div id="between-number-${fieldCount}" style="display: none; margin-top: 10px;">
                    <label for="between-number-amount-1-${fieldCount}">Between Number:</label>
                    <input type="text" name="between_number_amount_1_${fieldCount}" id="between-number-amount-2-${fieldCount}">
                    <label for="between-number-amount-2-${fieldCount}">And Number:</label>
                    <input type="text" name="between_number_amount_2_${fieldCount}" id="between-number-amount-2-${fieldCount}">
                </div>
                <div id="specific-number-${fieldCount}" style="display: none; margin-top: 10px;">
                    <label for="specific-number-amount-${fieldCount}">Specific Text:</label>
                    <input type="text" name="specific_number_amount_${fieldCount}" id="specific-number-amount-${fieldCount}">
                </div>

            `;

            container.appendChild(fieldDiv);
        }

        function handleTypeChange(index) {
            const typeSelect = document.getElementById(`type-${index}`);
            const foreignFields = document.getElementById(`foreign-fields-${index}`);
            const randomNumbFields = document.getElementById(`random-number-${index}`);
            const betweenNumbFields = document.getElementById(`between-number-${index}`);
            const randomTextFields = document.getElementById(`random-text-${index}`);
            const specificNumbFields = document.getElementById(`specific-number-${index}`);

            if (typeSelect.value === 'randnumb') {
                randomNumbFields.style.display = 'block';
            } else {
                randomNumbFields.style.display = 'none';
            }

            if (typeSelect.value === 'randtext') {
                randomTextFields.style.display = 'block';
            } else {
                randomTextFields.style.display = 'none';
            }

            if (typeSelect.value === 'randbetweennumb') {
                betweenNumbFields.style.display = 'block';
            } else {
                betweenNumbFields.style.display = 'none';
            }

            if (typeSelect.value === 'specnumb') {
                specificNumbFields.style.display = 'block';
            } else {
                specificNumbFields.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1>Generate PHP Seeder File</h1>
    <form action="" method="POST">
        <label for="title">Seeder Title:</label>
        <input type="text" id="title" name="title" required>
        <br><br>
        <label for="tabletitle">Table Name:</label>
        <input type="text" id="tabletitle" name="tabletitle" required>
        <br><br>

        <h3>Define Table Columns</h3>
        <div id="dynamic-fields"></div>
        <button type="button" onclick="addField()">+ Add Column</button>
        <br><br>
        <button type="submit">Generate File</button>
    </form>

    
</body>
</html>
