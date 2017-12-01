<?php

use Illuminate\Database\Seeder;
use App\Restaurant;

/**
 * Seeds restaurants.
 * https://laravel.com/docs/master/seeding
 */
class RestaurantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurants')->delete();

        // DB method
        /*
        DB::table('restaurants')->insert([
            'name'              => str_random(10),
            'email'             => str_random(10).'@gmail.com',
            'password'          => bcrypt('secret'),
        ]);
        */

        Restaurant::create(array(
            'name'              => 'Ramen 1 (yellow sign)',
            'location'          => 'Ramen Street',
            'type'              => 'ramen',
            'lunch_price'       => 800,
            'points'            => 'tonkostsu miso available, up to 2 free kaidama, choice soft/hard for men',
            'experience'        => 'very good ramen, strong taste, good price, not very confortable (narrow counter, crappy pop music...)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Ramen 2',
            'location'          => 'Ramen Street (further down)',
            'type'              => 'ramen',
            'lunch_price'       => 900,
            'points'            => '(dont remember)',
            'experience'        => 'good ramen, slightly more confortable than ramen 1 but slightly less tasty',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Ramen 3',
            'location'          => 'Ramen Street (in front of ramen 1)',
            'type'              => 'ramen',
            'lunch_price'       => 900,
            'points'            => '(dont remember anything special)',
            'experience'        => 'decent ramen, I didnt like much the meat which was containing too much fat for my enjoyment',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Ramen 4 NanaShi tonkotsu ramen',
            'location'          => 'Big street in front of the Robot Restaurant',
            'type'              => 'ramen',
            'lunch_price'       => 800,
            'points'            => 'tonkotsu available, a bit more confortable than the average ramen restaurant (larger counter, quiet jazz music)',
            'experience'        => 'decent ramen, but taste too light for me (I tried the awakoku aji tamago ramen)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Ramen 5 ',
            'location'          => 'next to the McDo of center gai (ramen itself is perpendicular street)',
            'type'              => 'ramen',
            'lunch_price'       => 900,
            'points'            => 'miso available (red, white and black), a bit more confortable than the average ramen restaurant (larger counter). Jazz music, but not so quiet. Some small tables are also available. No customization / question asked for the ramens.',
            'experience'        => 'disapointing: maybe decent ramen, but taste way too light for me. Could not feel the miso, men were a bit thick and yellow kind (not my favorite). Meat was good.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Ippudo',
            'location'          => '',
            'type'              => 'ramen',
            'lunch_price'       => 800,
            'points'            => 'fairly cistomizable ramen, one free kaidama available',
            'experience'        => 'very good ramen',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Burger King',
            'location'          => '',
            'type'              => 'burger',
            'lunch_price'       => 900,
            'points'            => '',
            'experience'        => '',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Wendy\'s first kitchen',
            'location'          => 'in front of BK',
            'type'              => 'burger',
            'lunch_price'       => 1000,
            'points'            => 'various stuff, but mostly famous for burgers. Interior slightly better than average similar chain (better than BK). Price slightly mor eexpensive.',
            'experience'        => 'Good burgers. But no better than BK, for more money.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Taco Bell',
            'location'          => '',
            'type'              => 'tacos',
            'lunch_price'       => 900,
            'points'            => '',
            'experience'        => '',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Turk Restaurant',
            'location'          => 'In front of H&M',
            'type'              => 'turk',
            'lunch_price'       => 900,
            'points'            => 'popular turkish dishes such as kehbab, possibility to eat inside on table',
            'experience'        => 'very good turk restaurant, good kehbab, not very confortable (narrow space)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Coco Ichi',
            'location'          => 'down the tower',
            'type'              => 'japanese curry',
            'lunch_price'       => 900,
            'points'            => 'highly customizable japanese curry',
            'experience'        => 'very good',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Jonathan\'s',
            'location'          => 'behind tower',
            'type'              => 'family restaurant',
            'lunch_price'       => 900,
            'points'            => 'wide choice of dishes, also deserts, comfortable',
            'experience'        => 'good',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Standing pizza bar Cona',
            'location'          => 'not far from ramen street',
            'type'              => 'pizzeria',
            'lunch_price'       => 500,
            'points'            => 'super cheap, standing at the counter (no chairs)',
            'experience'        => 'decent pizza for a super cheap 500 yen comes with a small salad and a drink. Pizza is a bit small so better be not too much hungry',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Something Terace',
            'location'          => 'couple of streets behind the office',
            'type'              => 'Indonesian',
            'points'            => 'nice table, nie restaurant, decent food and drinks',
            'experience'        => 'decent. had a nice evening there dining with 4 people',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Entertainment and sports bar',
            'location'          => 'next to samrat curry farther after the center gai',
            'type'              => 'cheap pizza/pasta lunch',
            'lunch_price'       => 500,
            'points'            => 'super cheap pizza or pasta, wide and dark sports bar (no windows) with sports video playing on many walls.',
            'experience'        => 'I don\'t really like this dark, no windows bar atmosphere, playing cheesy music, and I don\'t enjoy watching sports on TV. The pizza marguerita was pretty much tasteless. Other than that service fast, table and room spacious, and super cheap...',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Go Go Curry',
            'location'          => 'next to samrat curry farther after the center gai',
            'type'              => 'japanese curry',
            'lunch_price'       => 850,
            'points'            => 'chain restaurant a bit similar to cocoichi. customizable japanese curry, counter and small tables, basement floor',
            'experience'        => 'Good alternative to cocoichi. Less customizable, less options, but less expensive as well. Not really spicy, but good japanese curry. I enjoyed my chiken katsu with cheese. Basement is not very glamorous, but for a lunch it\'s OK. Loyalty System: Point card. 5 times: free toping. 10 times: free curry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Nice Pizza/Pasta restaurant',
            'location'          => 'in front of samrat curry farther after the center gai',
            'type'              => 'pizza/pasta/salad',
            'lunch_price'       => 1000,
            'points'            => '9th floor with nice large bay windows, spacious room with large tables. Real pizza oven. 1000yen menu set with a drink, bowl of soup, large salad, and large pizza. music playing, boom boom type, a bit loud but OK.',
            'experience'        => 'loved the room. fast service. taste was bland (marguerita).wtf: the deco is all fine except for taht reindeer head sprouting out of a column for no apparent reason',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Matsuya',
            'location'          => 'down the street from the office , with music shop, amp and drums, Floor: 1F, Windows: yes',
            'type'              => 'Japanese fast food',
            'lunch_price'       => 600,
            'points'            => 'Fast and good. Has counter and tables. Choice of curry or gyudon dishes.',
            'experience'        => 'I got a hamburger curry, it was really cheap and really good. Came fast toe. Perfect for a quick meal, but not very glamorous.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'mazesoba shichi',
            'location'          => 'down the street from the office',
            'type'              => 'Ramen',
            'lunch_price'       => 800,
            'points'            => 'Special ramen without soup.',
            'experience'        => 'Mind blowingly delicious. Meat is awesome, smokey taste, and in large quantity. The shoyu base sauce is delicious. Large counter. Japanese reggai music, not too loud. Extremely small stools. Avoid the ramen, way less delicious than the mazesoba. So you know that restaurant created by the gods in an expirement to feed mere mortals divine food? Shichi? Well it seems like, as in any gods stories, one of them was evil and planted a not-so-divine dish in the menu. I unveiled it and am here to warn you: if, for any unimaginable reasons, you still havent been there, when go, do NOT order the ramen. Stick with the mazu soba.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'pizza bar Cona',
            'location'          => 'down the street behind our office',
            'type'              => 'pizzeria',
            'lunch_price'       => 500,
            'points'            => 'cheap and light pizza, counter or tables available. 500 for just the pizza, +300yen for salad bar and drink bar. pasta lunch also available.',
            'experience'        => 'decent pizza 30cm, but light (very thin crust and topping), better not be hungry. Counter is small, not very confortable. When I went there it was also too cold.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'kin no kara',
            'location'          => 'next to ippudo, center gai',
            'type'              => 'fried chiken karahage take out only',
            'lunch_price'       => 400,
            'points'            => 'cheap, fast, to have on the go. Fried chicken or fries in a paper bag. Various sauce availble (including lemon juice).',
            'experience'        => 'simple, good, I use it if I still need some more food if my lunch was too light. Nowhere to sit down around there, so have to walk around while eating or just stand there.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'mangako thai chicken rice',
            'location'          => 'alongside the river down the big street our side',
            'type'              => 'thai',
            'lunch_price'       => 800,
            'points'            => 'supposedly famous chicken rice',
            'experience'        => 'My wife likes thai food quite a lot and has been in thailand a bunch of times. She told me there were a restaurant there serving chicken rice, and EVEN IN Thailand, there were a line. And the have a restaurant in Shibuya too. She\'s never been, but, if there is a line it HAS to be absolutely delicious, according to Japanese hyper standardized rules that can never been tricked (like say, some people paid to stay in line on day when the reporter is coming). So I went there, and arrived shortly in front a restaurant exposing dead chicken on its facade, a bit like youd expect in China. No line. I go inside, and its small and crappy, with shitty music on the top. I order the famous chicken rice, for a cost of f**ing 800yen. It comes quicklym with a soup (hot water) and some sauce (like sweet chinese shoyu with a touch of vinegar). The amount is ridiculously small (note that you could double it for 400yen). What\'s more, the chicken is actually half chicken, half chicken skin. I eat, nothing special at all. Rice is not really good. Chicken is nice and soft and tasteless, for the few bites I had on it. The sauce was good. Oh, and surprisingly enough, it also came with a mini-bucket full of pakuchi... this crap is usually expensive here. And that was it, EXTREMELY disapointed. The Singapour chicken rice we had (now closed) near Porters was 100 times better. I feel robbed. Of my money. My time. My hopes. And I\'m hungry.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Soul Kitchen Curry or something',
            'location'          => 'avenue behind the office',
            'type'              => 'japanese curry',
            'lunch_price'       => 800,
            'points'            => 'nice atmosphere. Elvis Presley music.',
            'experience'        => 'well balanced curry',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'ramen and curry',
            'location'          => 'right behing the office next to Shichi',
            'type'              => 'ramen',
            'lunch_price'       => 800,
            'points'            => 'tonkostsu ramen, soupless ramen, curry, gyoza... and mini versions available. Tried the white miso ramne, and it was prety bland, not worth the 800yen. I liked the atmosphere, very local, kinda cossy, with the TV on and the grand mothers as the staff.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Cafde. SALICA',
            'location'          => '2nd floor in front of Samrat',
            'type'              => 'cafe restaurant, sandwiches burgers desserts...',
            'lunch_price'       => 1100,
            'points'            => 'large space and tables, some with sofa, music OK and not too lound, a diverse menu',
            'experience'        => 'Once again I\'ve been blessed with a totally unexpected discovery of divinish food! I was going to Shibuya dowtown, wher ethe girls are pretty and the rayds are mighty, when suddenly an irresistible craving of premium burger got into me. Keeping my head cool, I thought "awwright no problemo, I can go to that restaurant I spot last month, "meat bank", that was advertising and appetizing burger. And so there I went. Confident. Looking at the menu before the entrance of the rather new establishment, I realized brutally that there were NO BURGER at all. What insolence. The world was crumbling all around me, I started feeling deeply cold and hungry and despaired and angry. I wandered around, crossing the street looking at other restaurants, which signs were blurred by my teary eyes. This is when I spotted a burger. Right there on a menu sign in the street. Some kind of cafe on the 2nd floor. My feeet directly driven by my empty stomach, I climbed the stairs. Nice surpise, the restaurant is spacious, empty of customers. Relaxing place. I order the avocado cheese burger, at a 1000 yen price tag with a drink. And... long story short, that ended up being one, if not the, but most likely top 6, best burger I ever had. And it looked gorgous too. And real delicious, at every levels, bread, lettuce, tomato, onions, sauce, french mustard, MEAT, cheese, avocado... all premium quality. Came served on a wooden palette too, for a little fanciness. So be advised, if you crave for premium burgers, do try this place! ',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Mediterranean Pizzeria',
            'location'          => 'behind office along the JR tracks',
            'type'              => 'italian',
            'lunch_price'       => 900,
            'points'            => 'inetrior OK. Pizza OK but really nothing special... 900 yen for pizza + salad. The salad (some lattuce) is small but the dressing is delicious. Tables and counter, not small.',
            'experience'        => 'Just OK.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Meat Bank',
            'location'          => 'that new building front of samrat  6F',
            'type'              => 'USA, burger, meat',
            'lunch_price'       => 1000,
            'points'            => 'inetrior nice. sofa and nice chairs. Lots of windows. Nice staff. Premium burgers. Good. Lunch set 1200 avocado  bacon cheese burger with drink and soup.',
            'experience'        => 'Confortable. Good taste. So I know you guys were expecting the a final conclusion to the the Asuka Tantan men paradox rumble, or "the singularity". But that will have to wait cause I needed fat food to lay a safety layer down my stomach, in preparation for tonight\'s apoliptycal debauchery. So I walked to a place I wanted to try, serving italian food. Unfortunately, I knew this place from 7 years ago, and it was not there anymore... so I had to improvise yet once again. After a while, I walked by the Meat Bank restaurant mentioned in earlier passage of my book "Tokyo Burgers Fight", chapter "The revenge of the Meat Bank - a striking return". And this time, they were displaying a menu with a choice of premium beef burgers, starting 1000yen. Since I just got a Charizar with good IV from a raid on my way there, I decided it was worth celebrating, and good for laying that fat layer I wanted. So up 6F I went. Very nice place, nice staff, nice sofa, and, good burger. Not mind blowing like the Cafde one, but definitely premium, with bacon as thick as a McDo patty. I paid 1200 yen for a bacon avocado cheese burger with a soup and a drink. Id say, nice place if you want to eat burger in a confy place with your girlfriend on a holloween friday night, avoiding stinking crowd and making that moment a bit special like it should be (this example was taken completly without any connection to any real upcoming event or targetted to particulart existing individual, leading a team or not)',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Asuka or something',
            'location'          => 'down the office',
            'type'              => 'Ramen',
            'lunch_price'       => 780,
            'points'            => 'Counter only. Narrow. Served fast. Very good tantan men.',
            'experience'        => 'We eat looking at the open kitchen. Staff was OK but not too polite / busy. Tantan men was maybe the best I had so far. Strong taste, hot but not too much (to me). And rather cheap (780).',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Samrat',
            'location'          => 'Samrat shibuya',
            'type'              => 'Indian curry',
            'lunch_price'       => 900,
            'points'            => 'Sucked',
            'experience'        => 'worst indian curry ever. When I first discovered indian curry near my office in setagaya-daita, it blew my mind so hard I ate it almost everyday. I just could not imagine eating anythnig else. Then years passed, I realized not all restaurants were equals, but still indian curry was always rhytming with delicious, greatly enjoyable meal. Then I worked in aoyama icchome and realized that the world can be a cold and heartless place. The indian food there was bland. Bland AF. Then I finally escaped what I thought was the darkest pit of restaurant and companies\' hell. To Shibuya! Surely one of the most amazing place to stroll for restaurants, and other things... But like in any movies and relationships, after all looked so perfect, something ugly, something world breaking happens and it all goes to shit. I ended up to Samrat shibuya today, and it sucked like I never thought it could suck. New low for indian curry, which now just joined the rank of my depressed thoughts about the world. Everything was bad, the interior was horrid, the nan was horribly oily, and the curry was doubtlessly the worst I ever had (and i asked for an easy one, the keema). Next time please somebody reconcile me with curry cause I dont feel I have the courage to face another one by myself.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Moss Burger',
            'location'          => '',
            'type'              => 'Burger',
            'lunch_price'       => 1000,
            'points'            => 'Japanese fast food burger.',
            'experience'        => 'Slow service. Overpriced. Impossible to eat by hands. I eat it with fork and knife at hme, and taste is good. But possibly the worst burger chain in Japan.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Kura',
            'location'          => 'after bic camera annex shibuya',
            'type'              => 'Italian',
            'lunch_price'       => 1000,
            'points'            => 'Decent italian. They have decent gorgonzola pizza. Good choice of pasta, pizza and salad, decent interior.',
            'experience'        => 'Interior was alright, I liked the choice on the menu, and got a decent gorgonzola pizza. I did not make me full, for 1000 yen cost, though.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'thai restaurant towards omotesando',
            'location'          => 'towards omotesando, 2nd floor',
            'type'              => 'Thai',
            'lunch_price'       => 1000,
            'points'            => 'Decent thai. Nice room, with windows bay.They have a gapao + green curry set, with good soup and small salad, for 980yen. Music. Open drink bar with tea and detox water.',
            'experience'        => 'Nice waitress, enjoyed the room and the set. Was good, but not especially delicious. Still well balanced set for a fair price.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'around that corner the best... symetrically opposite from Tower Record',
            'location'          => 'symetrically opposite from Tower Record',
            'type'              => 'Burgers and stuff',
            'lunch_price'       => 1000,
            'points'            => '1000 yen lunches and expensive 1100+ yen home made burgers. Possible topping customization includes jalapegno and gorgonzola. Big room. Windows. American music a bit loud.',
            'experience'        => 'Room OK. I got the jalapegno burger for 1300 yen. Came with a drink, and a mini soup served in an expresso cup. The burger was quite good, but not worth 1300 yen!',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Fast soba katsudon in front of big bic camera',
            'location'          => 'in front of big bic camera',
            'type'              => 'soba udon katsudon',
            'lunch_price'       => 750,
            'points'            => 'Decent sets. Fast food style. Narrow counter only. Buy at machine, get your set on a plate and find somewhere to sit.',
            'experience'        => 'Fast and cheap, and decent. I tried the zaru soba + katsudon for 750 yen. Made me full OK. Definitely not delicious, but decent.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Pizza Slice',
            'location'          => 'towards daikanyama',
            'type'              => 'pizza',
            'lunch_price'       => 1000,
            'points'            => 'big, heigh celing room with big windos bay. 500 yen american/newyork style pizza slices. Served fast. Just slices on a paper plate, no knife and fork. Free toppings include spicy stuff (but no oil), and oregan. Big tables avaialbe in addition of small and counter.',
            'experience'        => 'Pizza were decent. I liked the free oregan herbs available as topping on the tables. A bit overpriced though. Room is cool. When I was there, there were 29 young girls for 3 guys.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'iBeer',
            'location'          => 'Hikarie building 7F',
            'type'              => 'pizza, pasta, salad',
            'lunch_price'       => 1000,
            'points'            => 'Open space restaurant in nice Hikarie 7F. Busy floor, but nice setting. A bit expensive, but one of the cheapest restaurant in the building.',
            'experience'        => '1162 yen Pasta Set with Salad + bread + organic tomatoe avocado pasta. Nice salad, hot bread, and very, very good pasta. I would go as far as to call the pasta "exquisite". I also liked the setting, tables and chairs.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Avatar',
            'location'          => 'D side in front of shibuya language shool',
            'type'              => 'indian curry',
            'lunch_price'       => 850,
            'points'            => 'nice little indian restaurant. Not a chain. Relatively nice staff. No cheesy TV clips. Decent price curry sets. No keema. Free nan and rice.',
            'experience'        => 'Enjoyed the place, but the chiken curry I ordered was bland, tasteless.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Guzman y Gomez',
            'location'          => 'past tower record',
            'type'              => 'mexican',
            'lunch_price'       => 1300,
            'points'            => 'Good mexican fastfood cuisine. American chain. Expensive.',
            'experience'        => 'It just feels too grossly overpriced. The interior, and ordering system, is like a cheap fastfood. When I ate there the doors were kept opened despite the cold and I had to keep my jacket on. The music was very cheesy. I got 3 kedasilla for 1070 yen. It felt like a small snack. Definitely insufficient for a lunch. I left hungry and had to go eat something cheaper elsewhere. Taste-wise, it was good, and I could choose the meat amongst 8 different choices, for each of my 3 small kedasilla.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Il Fiume',
            'location'          => 'far',
            'type'              => 'italian',
            'lunch_price'       => 980,
            'points'            => 'Nnice place, nice interior, even has a terrace, in a calm street. Lunch has salad buffet, with decent variety of salads. Dish can be chosen from the menu. Japanese-italian style. No pizza.',
            'experience'        => 'Enjoyed the place. Salads were plenty, and good. I tried some pasta, 980yen, but it was... just decent. But it made me want to come back and try another dish.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Verde',
            'location'          => 'next to Elvis japanese curry place',
            'type'              => 'curry+gapoa+pizza',
            'lunch_price'       => 980,
            'points'            => 'Dark place on a 4th floor. Menu offers mostly indian curry sets, but also thai and pizza. Place doesnt look busy and there is a good amount of tables, so might be easy for large groups.',
            'experience'        => 'I tried the pizza+salad for 980yen. They have a real pizza oven to the crust was tasty from the wood fire. But other than that, it was quite plain. A lot of cheese on it though, so it made me well full. Salad was like the salad you get in a curry restaurant. The drink as well. I did not like how dark was the place. And the price was not cheap. So I won\'t be back. Fun note: the elevator to get there is extremely slow.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'some thai',
            'location'          => 'near office D side',
            'type'              => 'Thai',
            'lunch_price'       => 980,
            'points'            => 'gapao',
            'experience'        => 'very small tables, for most of them. Gapao was OK, came with good soup and small salad, from 980yen.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Blacows',
            'location'          => 'Daikanyama',
            'type'              => 'burger',
            'lunch_price'       => 2000,
            'points'            => 'expensive wagyu beef burgers, nice place',
            'experience'        => 'very good burger. Customizable. Expensive.',
            'visited'           => true,
            'visit_date'        => '2017-09-05 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Salasa',
            'location'          => 'Shibuya - Center gai',
            'type'              => 'izakaya',
            'lunch_price'       => 1050,
            'points'            => 'Various Japanese teishoku (lunch sets). Izakaya style. Small. Smallest tables ever. TV broadcasting news chanel. Slow service. Very good teishoku (lunch set)',
            'experience'        => 'The chef cooks your lunch from scratch apparently, so it takes up to 20 minutes of wait time. I was surprised because, in Japan, the service is usually very fast. But it was worth the wait as it was quite delicious. I ordered the cheese/porc fried rolls for 1050yen, and it came with pickles, miso soup, rice, and cabage. It was very good and I was fully satisfied.',
            'visited'           => true,
            'visit_date'        => '2017-11-28 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'JS Burger',
            'location'          => 'Shibuya',
            'type'              => 'burger',
            'lunch_price'       => 1500,
            'points'            => 'Very nice place (confortable, various tables, big windows bay, enjoyable music). Expensive. Premium burgers. Salad bar at lunch.',
            'experience'        => 'Very nice experience, nejoyed the place and the music. Loved the salad bar, with lots of choices, and good. Burger came fast. I tried the JS burger. The ingredient were undeniably good, but globally, it was lacking taste as there were almost no sauce. Still, I ended up full and satisfied.',
            'visited'           => true,
            'visit_date'        => '2017-11-29 13:00:00'
        ));

        Restaurant::create(array(
            'name'              => 'Pizza Salvatore Cuomo',
            'location'          => 'Shibuya',
            'type'              => 'italian',
            'lunch_price'       => 1280,
            'points'            => 'Usually for delivery, but also has a nice interior with tables and counter, as well as a terrace (with heaters). 100yen pasta lunch, or 1280 yen pizza lunch. Lunch set includes a salad, a mini soup, and a drink. Decent but overpriced.',
            'experience'        => 'I was suprized at how nice the interior was. Did not like the music though (some sort of modern hip-hop, kind of loud). I tried the gorgonzola+mozarealla pizza. It was decent, but a bit small. 1280 yen felt overpriced.',
            'visited'           => true,
            'visit_date'        => '2017-11-30 13:00:00'
        ));

    }
}
