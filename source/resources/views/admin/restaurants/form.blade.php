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
            {!! Form::number('lunch_price', null,
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
           {!! Form::radio('visited', true, true,
              array(
                'placeholder' => 'yes',
                'class' => 'form-control')) !!}
           {!! Form::radio('visited', false, false,
              array(
                'placeholder' => 'no',
                'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Visit date:</strong>
            {!! Form::date('visit_date', null,
                      array('placeholder' => 'Visit date',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Google Maps Link:</strong>
            {!! Form::url('google_maps_link', null,
                      array('placeholder' => 'Google Maps Link',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Tabelog Link:</strong>
            {!! Form::url('tabelog_link', null,
                      array('placeholder' => 'Tabelog Link',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Official Website:</strong>
            {!! Form::url('official_website', null,
                      array('placeholder' => 'Official Website',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Score Global:</strong>
            {!! Form::number('score_lunch', null,
                      array('placeholder' => 'Score Lunch',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Score Place:</strong>
            {!! Form::number('score_place', null,
                      array('placeholder' => 'Score Place',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Score Food:</strong>
            {!! Form::number('score_food', null,
                      array('placeholder' => 'Score Food',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Score Price:</strong>
            {!! Form::number('score_price', null,
                      array('placeholder' => 'Score Price',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Score Date:</strong>
            {!! Form::number('score_date', null,
                      array('placeholder' => 'Score Date',
                            'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
