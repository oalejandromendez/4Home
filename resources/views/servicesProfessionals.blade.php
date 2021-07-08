<!DOCTYPE html>
 <html lang="en-EN">
  <head>
    <meta charset="utf-8">
    <style>
        .label-head {
            background-color: #67aeff;
            font-size: 22px;
            text-align-last: center;
            color: white;
            font-weight: bold;
            padding: 25px 25px;
            margin-bottom: 0px;
        }
        .body{
          padding: 25px 25px;
          border: 1px solid #67aeff;
        }
        .style-name {
          font-size: 13px;
          font-weight: bold;
          margin-top: 25px;
        }
        .footer {
          padding: 25px 25px;
          background-color: #67aeff;
          text-align: center;
          color: white;
          font-weight: bold;
        }

        .span-name {
            font-size: 14px;
            font-weight: bold;
        }

        .mt-25 {
            margin-top: 25px;
        }

        .mb-25 {
            margin-bottom: 25px;
        }

    </style>
  </head>
  <body>
    <h5 class="label-head">4HOME - AGENDA DIARIA</h5>
    <div class="body">
        <span class="span-name">Hola {{ $fullname }}!</span>
        <br>
        <p>Tu agenda para el dia de mañana es la siguiente:</p>

        <div>
            <strong>Referencia #</strong><span>{{$reference}}</span>
            <br>
            <strong>Cliente: </strong><span>{{$client}}</span>
            <br>
            <strong>Dirección: </strong><span>{{$address}}</span>
            <br>
            <strong>Jornada: </strong><span>{{$workingDay}}</span>
            <br>
        </div>

        <span class="style-name">4HOME!</span>
    </div>
    <div class="footer">
      &copy;
      <span id="copyright">
          <script>document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))</script>
      </span>
      4HOME S.A.S
    </div>
  </body>
 </html>
