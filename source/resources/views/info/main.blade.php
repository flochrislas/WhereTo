@extends('layouts.app')

@section('title', 'Info')

@section('content')
    <div>Disclaimer</div>
    All content is by me from my personal experience, judgement and opinion.
    Naturally, individual experience, or taste, may differ.

    <div>Going for lunch</div>
      Lunch time: most establishments are usually take orders between 11am and 2:30pm. Sometimes more, but if you fall outside this time window, it is better to double-check if it is opened, by looking on the Google or Tabelog linked page.
      Ratings
        Ratings are made by me alone, after going to have lunch at a place one or multiple times. I beleive this makes for more consistent ratings than hundreds of different people rating different places.

          Lunch (Global enojoyment)
          0: Absolutely horrible
          1: Meh, I don't think I will go again
          2: OK, I might go again in some specific context (craving this specific food, feeling rich...)
          3: Very good. I would go again any time
          4: Excellent. It is hard to refrain going back to that place every other days

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
        There are some untold rules such as:
          Walking on the left side (not in the middle, not on the right side)
          Sticking on the left side in the escalators if you are not climbing the stairs
          No smoking, unless some sign is specifically saying you can
          Japanese people don't eat or drink while walking
          Japanese poeple use their phone while walking, but they should not
          It is OK to ride a bycicle on the sidewalk
          Japanese people use umbrella any time it rains. When entering a shop or restaurant, you should fold it, and either let it in a rack outside, or use a plastic cover or a dryer if available at the entrance. The purpose is to prevent your umbrella to drop water on the floor inside.

      Seasons
        Summer
          Rainy until mid-July, then very hot and humid. Do not plan to stay outside for extensive period of time. Plan to cool down indoor regularly.
        Autumn
          Great season. Mostly great weather.
        Winter
          Mostly sunny, clear and cold weather. Gets really cold around February.
        Srping
          Good weather alternates with shitty weather... It is a mix of warm blue sky and grey cold rainy days.
    @include('payments.donations')
    @include('ads.baner')
@endsection
