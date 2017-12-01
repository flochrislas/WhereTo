<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {!! Form::text('name', null,
                      array('placeholder' => 'Name',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Location:</strong>
            {!! Form::text('location', null,
                      array('placeholder' => 'Location',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Coordinates:</strong>
            {!! Form::text('coord', null,
                      array('placeholder' => 'Coordinates',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Type:</strong>
            {!! Form::text('type', null,
                      array('placeholder' => 'Type',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Typical lunch price:</strong>
            {!! Form::text('lunch_price', null,
                      array('placeholder' => 'Typical lunch price',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Points:</strong>
            {!! Form::textarea('points', null,
                      array('placeholder' => 'Points',
                            'class' => 'form-control',
                            'style'=>'height:100px')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Personal experience:</strong>
            {!! Form::textarea('experience', null,
                      array('placeholder' => 'Personal experience',
                            'class' => 'form-control',
                            'style'=>'height:150px')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Visited:</strong>
            {!! Form::text('visited', null,
                      array('placeholder' => 'Visited',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Visit date:</strong>
            {!! Form::text('visit_date', null,
                      array('placeholder' => 'Visit date',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
