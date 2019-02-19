@extends('layouts.app')

@section('title', 'Info')

@section('content')
    <div>Disclaimer</div>
    All content is by me from my personal experience, judgement and opinion.
    Naturally, individual experience, or taste, may differ.

    <div>Going for lunch</div>
      Lunch time: usually opened between 11am and 2:30pm. Sometimes more, but if you fall outside this time window, it is better to double-check if it is opened, by looking on the Google or Tabelog linked page.
      Ratings
        Ratings are made by me alone, after going to have lunch to a place one or multiple times. I beleive this makes for more consistent ratings than hundreds of different people rating different places.

          Lunch (Global enojoyment)
          0: Absolutely horrible
          1: Bad or whatever, I won't go again
          2: OK, I might go again if I crave for this specific kind of lunch / or if I have extra money I can spare, etc...
          3: Very good. I would be down to go again any time
          4: Excellent. It is hard to refrain to go back to that place every week or more

          Food (Taste)
          0: poor
          1: good
          2: delicious
          3: divine

          Place (Atmosphere)
          0: horrible
          1: decent
          2: very nice
          3: fantastic

          Price (How adequate is the price)
          0: overpriced
          1: correct
          2: good deal

          Dates (How nice for dating)
          0: bad for dates
          1: decent for dates
          2: great for dates
          
    <div>Shibuya Wi-Wi-Fi: Around Shibuya, and especially near malls, free WiFi Wi-Wi-Fi is available. You can select it, acess your web browser, register once (quick and easy), and done. Check areas here: https://www.atpress.ne.jp/news/165698</div>
    <div>Going for diner</div>
    <div>Going for a drink</div>
    <div>Clubbing</div>
    <div>Karaoke</div>
    <div>Shopping</div>
    <div>Hotels</div>
    <div>Game centers</div>
    <div>Combini</div>
    <div>Cafes</div>
    <div>Internet cafes</div>
    <div>Toilets</div>
    <div>General information</div>
      Walking around
        There is some untold rules such as:
          Walking on the left side (not in the middle, not on the right side)
          Sticking on the left side in the escalators if you are not climbing the stairs
          No smoking unless some sign is specifically saying you can

      Seasons
        Summer
          Very hot and humid. Do not plan to stay outside for extensive period of time. Plan to cool down indoor regularly.
        Autumn
          Great season. Mostly great weather.
        Winter
          Mostly sunny, clear and cold weather. Gets really cold around February.
        Srping
          Good weather alternates with shitty weather... It is a mix of warm blue sky and grey cold rainy days.
    @include('payments.donations')
    @include('ads.baner')
@endsection
