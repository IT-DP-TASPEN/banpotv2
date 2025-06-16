@php
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: 21.5cm 11cm;
            margin: 0.5cm;
        }

        .container {
            display: grid;
            grid-template-columns: 4fr 1fr 15fr;
            /* grid-template-rows: 0.05 0.05 0.05 0.05; */
            /* grid-gap: -1230px; */
            background-color: dodgerblue;
            /* padding: 5px; */
            margin-top: 2px;
            padding-top: 4px;
            padding-left: 69.5px;
            margin-bottom: 10px;


            /* Add some space between containers */
        }

        .container-isi {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            /* Four equal columns */
            background-color: dodgerblue;
            /* padding: 5px; */
        }

        .container-ttd {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Four equal columns */
            background-color: dodgerblue;
            padding: 5px;
        }

        .container>div {
            padding: 3px;
            font-size: 12px;
            text-align: left;
        }

        .container-isi>div {
            padding: 2px;
            font-size: 12px;
            text-align: left;
            /* Align text to the left for the first container */
        }

        .container-isi>div {
            text-align: center;
            /* Center text for the second container */
        }

        .nomor_bilyet {
            padding-top: 85px;
            display: flex;
            /* Use flexbox */
            justify-content: center;
            /* Center horizontally */
            align-items: center;
            /* Center vertically */
            height: 30px;
            /* Set a height for vertical centering */
            font-size: 12px;
            /* Adjust font size */
            margin-bottom: 1px;
            /* Add some space below */
        }

        .head-isi {
            border-bottom: 1px solid black;
            font-size: 12px;
            /* Adjust font size for headers */
            font-weight: none;
            /* Make headers bold */
            text-align: center;
            /* Center align headers */
            padding: 5px;
            /* Add padding for better spacing */
            margin-left: 10px;
            margin-right: 10px;
        }

        .head-isi-ttd {
            font-size: 12px;
            /* Adjust font size for headers */
            font-weight: none;
            /* Make headers bold */
            text-align: left;
            /* Center align headers */
            padding: 5px;
            /* Add padding for better spacing */
            margin-left: 10px;
            margin-right: 10px;
        }

        /* .head-isi-ttd-1 {
            font-size: 12px;
            
            font-weight: none;
           
            text-align: right;
           
            margin-top: 20px;
            margin-left: 10px;
            margin-right: 10px;
        } */

        .head-isi-ttd-2 {
            font-size: 12px;
            /* Adjust font size for headers */
            font-weight: none;
            /* Make headers bold */
            text-align: right;
            /* Center align headers */
            margin-top: 20px;
            /* Add padding for better spacing */
            margin-left: 10px;
            margin-right: 10px;
        }

        /* .head-isi-ttd-3 {
            font-size: 12px;
            
            font-weight: none;

            text-align: left;
            margin-bottom: 1px;
            margin-top: 20px;
            margin-left: 10px;
            margin-right: 10px;
        } */

        .container-ttd {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            /* Mengatur seluruh konten ke kanan */
            background-color: dodgerblue;
            padding: 2px;
            padding-right: 9px;
            /* margin-right: 7px; */
        }

        .signature-group {
            display: flex;
            margin-top: 48px;
            /* Jarak dari tanggal */
        }

        .signature-block {
            display: flex;
            flex-direction: column;
        }

        .head-isi-ttd-1,
        .head-isi-ttd-3 {
            text-align: right;
            margin: 0;
            padding-left: 90px;
            font-size: 12px;
        }

        .signature-position {
            margin-top: 5px;
            /* Mengurangi jarak antara nama dan jabatan */
        }


        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-button {
                display: none;
            }
        }
    </style>
    <script>
        window.onload = function () {
            window.print();
        }


    </script>

</head>

<body>

    <div class="container">
        <div class="nomor_bilyet">
            <span style="font-weight: bold;">{{ $tab->permintaan_id }}</span>
        </div>
        <div class="head-isi">
            <span style="font-weight: bold;">Formulir Permohonan Pembukaan Rekening</span>
        </div>
    </div>

</body>

</html>