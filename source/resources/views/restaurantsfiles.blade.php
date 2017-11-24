// items.blade.php tutorial from:
// https://appdividend.com/2017/06/12/import-export-data-csv-excel-laravel-5-4

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Import-Export Data</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"
  </head>
  <body>
    <div class="container">
      <br />
      <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-6">
          <div class="row">
            <form action="{{url('restaurants/import')}}" method="post" enctype="multipart/form-data">
              <div class="col-md-6">
                {{csrf_field()}}
                <input type="file" name="imported-file"/>
              </div>
              <div class="col-md-6">
                  <button class="btn btn-primary" type="submit">Import</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-2">
          <form action="{{url('restaurants/export')}}" enctype="multipart/form-data">
            <button class="btn btn-success" type="submit">Export</button>
          </form>
        </div>
      </div>
      <div class="row">
        @if(isset($restaurants) && count($restaurants))
        <table class="table table-striped">
          <thead>
            <tr>
              <td>name</td>
              <td>location</td>
              <td>type</td>
              <td>lunch_price</td>
              <td>points</td>
              <td>experience</td>
            </tr>
          </thead>
          @foreach($restaurants as $restaurant)
            <tr>
              <td>{{$restaurant->name}}</td>
              <td>{{$restaurant->location}}</td>
              <td>{{$restaurant->type}}</td>
              <td>{{$restaurant->lunch_price}}</td>
              <td>{{$restaurant->points}}</td>
              <td>{{$restaurant->experience}}</td>
            </tr>
          @endforeach
        </table>
        @endif
      </div>
    </div>
  </body>
</html>
