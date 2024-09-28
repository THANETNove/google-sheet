<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Data from Google Sheets API</title>
</head>

<body>
    <h1>General Ledger Data</h1>
    <div id="data-container">
        <!-- ข้อมูลจะถูกแสดงที่นี่ -->
    </div>

    <script>
        let url = 'https://api.sheety.co/63bdf448a887f4e3f0e8628cb7dac685/generalLedger/generalLedger';

        fetch(url)
            .then((response) => response.json())
            .then(json => {
                // รับข้อมูลจาก API
                let generalLedgers = json.generalLedger;
                let output = '';


                console.log("generalLedgers", generalLedgers);
                // วนลูปเพื่อแสดงข้อมูลแต่ละรายการ
                generalLedgers.forEach(ledger => {
                    output +=
                        <
p> <strong> Date: </strong> ${ledger.date}</>
                        <
                        p > < strong > Description: < /strong> ${ledger.description}</p>
                        <p> <strong> Amount: < /strong> ${ledger.amount}</p>
                        <hr> ;
                });

                // แสดงข้อมูลใน div ที่มี id="data-container"
                document.getElementById('data-container').innerHTML = output;
            })
            .catch(error => console.log('Error fetching data:', error));
    </script>
</body>

</html>
