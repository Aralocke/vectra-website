<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$Style = array(
    1 => 'Melee',
    2 => 'Ranged',
    3 => 'Magic'
) ;

$Shops[1] = "Aaron's Archery Appendages";
$Shops[2] = "Agmundi Quality Clothes";
$Shops[3] = "Al-Kharid General Store";
$Shops[4] = "Alain's Farming Patch";
$Shops[5] = "Aleck's Hunter Emporium";
$Shops[6] = "Ali's Discount Basic Rune Shop";
$Shops[8] = "Ali's Discount Blackjack (d) Shop";
$Shops[7] = "Ali's Discount Blackjack (o) Shop";
$Shops[9] = "Ali's Discount Desert Clothing Shop";
$Shops[10] = "Ali's Discount Menaphite Clothing Shop";
$Shops[11] = "Ali's Discount Rune Shop";
$Shops[12] = "Ali's Discount Wares";
$Shops[13] = "Ali's Water Cart";
$Shops[14] = "Alice's Farming Shop";
$Shops[15] = "Ardougne Baker's Stall";
$Shops[16] = "Ardougne Fur Stall";
$Shops[17] = "Ardougne Gem Stall";
$Shops[18] = "Ardougne Silver Stall";
$Shops[19] = "Ardougne Spice Stall";
$Shops[20] = "Arhein Store";
$Shops[21] = "Armour Shop";
$Shops[22] = "Armour Store";
$Shops[23] = "Armoury";
$Shops[24] = "Aubury's Rune Shop";
$Shops[25] = "Aurel's Supplies";
$Shops[26] = "Ava's Odds and Ends";
$Shops[27] = "Baba Yaga's Magic Shop";
$Shops[28] = "Bandit Duty Free";
$Shops[29] = "Barkers' Haberdashery";
$Shops[220] = "Bedabin Village Bartering";
$Shops[30] = "Betty's Magic Emporium";
$Shops[31] = "Blackjack Seller";
$Shops[221] = "Blades by Urbi";
$Shops[32] = "Blue Moon Inn";
$Shops[33] = "Blurberry Bar";
$Shops[222] = "Blurberry's Bar";
$Shops[34] = "Bob's Brilliant Axes";
$Shops[35] = "Bolkoy's Village Shop";
$Shops[36] = "Bolongo's Farming Patch";
$Shops[37] = "Brian's Archery Supplies";
$Shops[38] = "Brian's Battleaxe Bazaar";
$Shops[39] = "Burthorpe Supplies";
$Shops[40] = "Candle Shop";
$Shops[41] = "Carefree Crafting Stall";
$Shops[42] = "Cassie's Shield Shop";
$Shops[43] = "Castle Wars Ticket Exchange";
$Shops[234] = "Commander Loman's reward shop";
$Shops[44] = "Construction Supplies";
$Shops[217] = "Contraband Yak Produce";
$Shops[45] = "Crossbow Shop";
$Shops[46] = "Crystal Equipment";
$Shops[47] = "Daga's Scimitar Smithy";
$Shops[48] = "Dancing Donkey Inn";
$Shops[49] = "Dantaera's Farming Patch";
$Shops[50] = "Darren's Wilderness Cape Shop";
$Shops[51] = "Davon's Amulet Store";
$Shops[52] = "Dead Man's Chest";
$Shops[53] = "Diango's Toy Store";
$Shops[223] = "Dommik's Crafting Store";
$Shops[54] = "Dommiks Crafting Store";
$Shops[55] = "Dorgesh-Kaan General Supplies";
$Shops[56] = "Draynor Seed Market";
$Shops[57] = "Drogo's Mining Emporium";
$Shops[58] = "Dwarven Shopping Store";
$Shops[59] = "Edgeville General Store";
$Shops[60] = "Edmond's Wilderness Cape Shop";
$Shops[61] = "Ellena's Farming Patch";
$Shops[224] = "Elstan's Farming Patch";
$Shops[62] = "Etceteria Fish";
$Shops[63] = "Falador General Store";
$Shops[232] = "Faruq's Tools for Games";
$Shops[64] = "Fayeth's Farming Patch";
$Shops[65] = "Fine Fashions";
$Shops[216] = "Fist of Guthix Reward Shop";
$Shops[66] = "Flying Horse Inn";
$Shops[67] = "Flynn's Mace Market";
$Shops[69] = "Francis's Farming Patch";
$Shops[70] = "Fremennik Fish Monger";
$Shops[71] = "Fremennik Fur Trader";
$Shops[72] = "Frenita's Cookery Shop";
$Shops[212] = "Fresh Meat";
$Shops[73] = "Frincos Fabulous Herb Store";
$Shops[74] = "Funch's Fine Groceries";
$Shops[75] = "Gabooty's Tai Bwo Wannai Cooperative";
$Shops[76] = "Gabooty's Tai Bwo Wannai Drinky Store";
$Shops[77] = "Garth's Farming Patch";
$Shops[78] = "General Store";
$Shops[79] = "Gerrant's Fishy Business";
$Shops[80] = "Gertrude's Cats";
$Shops[213] = "Gift Shop";
$Shops[81] = "Grand Tree Groceries";
$Shops[82] = "Green Gemstone Gems";
$Shops[167] = "Grud's Herblore Store";
$Shops[83] = "Grum's Gold Exchange";
$Shops[84] = "Gulluck and Sons";
$Shops[85] = "Gunslik's Assorted Items";
$Shops[86] = "Hair of the Dog Tavern";
$Shops[87] = "Hamab's Crafting Emporium";
$Shops[88] = "Happy Heroes' H'emporium";
$Shops[89] = "Harry's Fishing Shop";
$Shops[90] = "Helmet Shop";
$Shops[91] = "Herquin's Gems";
$Shops[92] = "Heskel's Farming Patch";
$Shops[93] = "Hicktons Archery Emporium";
$Shops[94] = "Honest Jimmy's House of Stuff";
$Shops[95] = "Horvik's Armour Shop";
$Shops[96] = "Ifaba's General Store";
$Shops[97] = "Initiate Rank Armory";
$Shops[98] = "Irksol";
$Shops[226] = "Irksol's Ruby Rings";
$Shops[99] = "Island Fishmonger";
$Shops[100] = "Island Greengrocer";
$Shops[101] = "Jakut";
$Shops[227] = "Jamila's Craft Stall";
$Shops[102] = "Jatix Herblore Shop";
$Shops[228] = "Jatix's Herblore Shop";
$Shops[103] = "Karamja General Store";
$Shops[104] = "Karamja's Wines, Spirits, and Beers";
$Shops[105] = "Kebab Shop";
$Shops[106] = "Keepa Kettilon's store";
$Shops[107] = "Keldagrim Stonemason";
$Shops[108] = "Keldagrim's Best Bread";
$Shops[109] = "Khazard General Store";
$Shops[219] = "King Lathas's Armoury";
$Shops[110] = "King's Axe Inn";
$Shops[111] = "Kragen's Farming Patch";
$Shops[112] = "Lady of the Waves Tickets";
$Shops[113] = "Larry's Wilderness Cape Shop";
$Shops[114] = "Laughing Miner Pub";
$Shops[115] = "Legends' Guild General Store";
$Shops[116] = "Legends' Guild Shop of Useful Items";
$Shops[117] = "Lletya Archery store";
$Shops[118] = "Lletya Food Store";
$Shops[119] = "Lletya General Store";
$Shops[120] = "Lletya Seamstress";
$Shops[236] = "Lord Marshal Brogan's reward shop";
$Shops[121] = "Louies' Armoured Legs Bazaar";
$Shops[211] = "Lovecraft's Tackle";
$Shops[122] = "Lowe's Archery Emporium";
$Shops[238] = "Lumbridge Fishing Supplies";
$Shops[123] = "Lumbridge General Store";
$Shops[124] = "Lyra's Farming Patch";
$Shops[125] = "Martin Thwait's Lost and Found";
$Shops[126] = "Miltog's Lamps";
$Shops[127] = "Miscellanian Clothes Shop";
$Shops[128] = "Miscellanian Food Shop";
$Shops[129] = "Moon Clan Fine Clothes";
$Shops[130] = "Nardah General Store";
$Shops[229] = "Nathifa's Bake Stall";
$Shops[131] = "Neil's Wilderness Cape Shop";
$Shops[132] = "Nurmof's Pickaxe Shop";
$Shops[133] = "Obli's General Store";
$Shops[242] = "Ore Seller";
$Shops[134] = "Ore Store";
$Shops[135] = "Oziach";
$Shops[136] = "Paramaya Inn";
$Shops[214] = "Pet Shop";
$Shops[137] = "Pickaxe-Is-Mine";
$Shops[138] = "Pie Shop";
$Shops[237] = "Poison Arrow Pub";
$Shops[139] = "Pollnivneach General Store";
$Shops[140] = "Port Khazard Bar";
$Shops[141] = "Quality Armour Shop";
$Shops[142] = "Quartermaster's Store";
$Shops[230] = "Raetul and Co's Cloth Store";
$Shops[143] = "Ranael's Super Skirt Store";
$Shops[144] = "Rasolo the Wandering Merchant";
$Shops[145] = "Reldak's Leather Armour";
$Shops[146] = "Rellekka Longhall Bar";
$Shops[147] = "Rhazien's Farming Patch";
$Shops[148] = "Richard's Farming Shop";
$Shops[149] = "Richard's Wilderness Cape Shop";
$Shops[150] = "Rimmington General Store";
$Shops[151] = "Rok's Chocs Box";
$Shops[225] = "Rometti's Fine Fashions";
$Shops[152] = "Rommik's Crafting Supplies";
$Shops[153] = "Rufus' Meat Emporium";
$Shops[218] = "Runecrafting Guild Rewards";
$Shops[154] = "Rusty Anchor Inn";
$Shops[155] = "Sam's Wilderness Cape Shop";
$Shops[156] = "Sarah's Farming Shop";
$Shops[157] = "Seddu's Adventurer's Shop";
$Shops[158] = "Selena's Farming Patch";
$Shops[233] = "Serjeant Cole's reward shop";
$Shops[159] = "Shantay Pass Shop";
$Shops[160] = "Shop of Distaste";
$Shops[161] = "Sigmund The Merchant";
$Shops[162] = "Silver Cog Silver Stall";
$Shops[163] = "Simon's Wilderness Cape Shop";
$Shops[164] = "Slayer Equipment";
$Shops[165] = "Smithing Smith's Shop";
$Shops[166] = "Snop Dal's Ogre General Supplies";
$Shops[168] = "Solihib's Food Stall";
$Shops[215] = "Summoning Supplies";
$Shops[169] = "Tamayu's Spear Stall";
$Shops[170] = "Taria's Farming Patch";
$Shops[171] = "The Asp and Snake Bar";
$Shops[172] = "The Big Heist Lodge";
$Shops[173] = "The Dragon Inn";
$Shops[174] = "The Esoterican Arms";
$Shops[175] = "The Forester's Arms";
$Shops[176] = "The Jolly Boar Inn";
$Shops[177] = "The Other Inn";
$Shops[178] = "The Rising Sun Inn";
$Shops[179] = "The Shrimp and Parrot";
$Shops[180] = "The Spice Is Right";
$Shops[181] = "The Toad and Chicken";
$Shops[182] = "Thessalia Fine Clothes";
$Shops[183] = "Tiadeche's Karambwan Stall";
$Shops[184] = "Tony's Pizza Bases";
$Shops[185] = "Torrell's Farming Patch";
$Shops[210] = "Trader Sven's Black Market Goods";
$Shops[186] = "Tutab's Magical Market";
$Shops[187] = "Two Feet Charley's Fish Shop";
$Shops[188] = "TzHaar-Hur-Lek's Ore and Gem Store";
$Shops[189] = "TzHaar-Hur-Tel's Equipment Store";
$Shops[190] = "TzHaar-Mej-Roh's Rune Store";
$Shops[209] = "Uglug's Stuffsies";
$Shops[191] = "Vanessa's Farming Shop";
$Shops[192] = "Varrock General Store";
$Shops[231] = "Varrock Sword Shop";
$Shops[193] = "Varrock Swordshop";
$Shops[194] = "Vasquen's Farming Patch";
$Shops[195] = "Vermundi's Clothes Stall";
$Shops[196] = "Vigr's Warhammers";
$Shops[197] = "Void Knight Archery Store";
$Shops[198] = "Void Knight General Store";
$Shops[199] = "Void Knight Magic Store";
$Shops[235] = "War-chief Reeves's reward shop";
$Shops[200] = "Warriors' Guild Armoury";
$Shops[241] = "Warriors' Guild Food Shop ";
$Shops[240] = "Warriors' Guild Potion Shop ";
$Shops[201] = "Wayne's Chains! - Chainmail Specialist";
$Shops[202] = "Weapons Galore";
$Shops[203] = "West Ardougne General Store";
$Shops[204] = "White Knight Master Armoury";
$Shops[243] = "Wine Shop";
$Shops[68] = "Wydin's Food Store";
$Shops[245] = "Wyson's Woad Leaves";
$Shops[205] = "Yrsa's Accoutrements";
$Shops[206] = "Zaff's Superior Staves";
$Shops[244] = "Zanaris General Store";
$Shops[207] = "Zeke's Superior Scimitars";
$Shops[208] = "Zenesha's Plate Mail Body Shop";

$Races = array(0 => 'Unknown', 1 => "Beast", 2 => "Undead", 3 => "Demon", 4 => "Human", 5 =>
    "Elemental", 6 => "Animal", 7 => "Monkey", 8 => "Goblin", 9 => "Animated", 10 =>
    "Troll", 11 => "Avatar", 12 => "Aviansie", 13 => "Dragon", 14 => "Snail", 15 =>
    "Gnome", 16 => "God", 17 => "Bird", 18 => "Golem", 19 => "Dwarf", 20 => "Giant",
    22 => "Rat", 23 => "Ogre", 24 => "Icyene", 25 => "Vampire", 26 => "Ghost", 27 =>
    "Cyclops", 28 => "Arachnid", 29 => "Spawn", 30 => "Duck", 31 =>
    "Earth Elemental", 32 => "Elf", 33 => "Insect", 34 => "Ourg", 36 => "Bug", 37 =>
    "Rodent", 38 => "Snake", 40 => "Wolf", 41 => "Fiend", 42 => "Food", 44 =>
    "Horror", 45 => "Tzhaar", 46 => "Energy", 47 => "Spirit", 48 => "Mahjarrat", 50 =>
    "Orc", 51 => "Possessed", 52 => "Rock", 53 => "Dog", 54 => "Leprechaun", 55 =>
    "Undead Wyvern", 56 => "Plant", 57 => "Mutation", 58 => "Experiment", 60 =>
    "Fairy", 63 => "Snowman", 64 => "Scarab", 65 => "Zombie", 66 => "Earwig", 67 =>
    "Ent", 68 => "Cat", 69 => "Ghoul", 72 => "Ape", 73 => "Imp", 74 => "Familiar",
    75 => "Dagannoth", 76 => "Dorgeshuun") ;

$Search = null ;
if (!empty($_GET['search']))
{
    $Search = urldecode(trim($_GET['search'])) ;
}

# Credits
echo 'NOTICE: This data is kindly provided with permission by <a href="http://zybez.net">Zybez.net</a>' . chr(10) ;
if (empty($Search))
{
    echo 'ERROR: Missing argument &search' . chr(10) ;
}
else
{
    # Connect to the database
    $Dbc->connect() ;

    if (is_numeric($Search))
    {
        $Query = "SELECT * FROM monster WHERE id = '" . (int)$Search . "'" ;
        $Result = $Dbc->sql_query($Query) ;
        $Result = ($Dbc->sql_num_rows($Result) > 0) ? $Result : false ;
    }
    else
    {
        $Result = sqlSearch('Parsers', 'monster', 'name', $Search, 5) ;
    }

    if ($Result == false)
    {
        echo 'ERROR: Nothing found for your search ' . $Search . chr(10) ;
    }
    else
    {
        $Results = $Dbc->sql_num_rows($Result) ;
        echo 'RESULTS: ' . $Results . chr(10) ;
        if ($Results > 1)
        {
            $Count = 0 ;
            while ((($Obj = $Dbc->sql_fetch($Result)) !== null) && $Count < 10)
            {
                echo 'NPC: ' . str_replace(' ', '_', $Obj->name) . ' #' . $Obj->id . chr(10) ;
                $Count++ ;
            }
        }
        else
        {
            $Obj = $Dbc->sql_fetch($Result) ;
            echo 'NAME: ' . str_replace(' ', '_', $Obj->name) . chr(10) ;
            $Link = 'http://www.zybez.net/npc.aspx?id=' . $Obj->id ;
            echo 'ID: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10) ;
            echo 'MEMBERS: ' . (($Obj->members == 1) ? 'Yes' : 'No') . chr(10) ; 
            echo 'RACE: ' . $Races[$Obj->race] . chr(10) ;      
            if ($Obj->shop != 0) 
            {
                echo 'SHOP: ' . $Shops[$Obj->shop] . chr(10) ;
                echo 'TYPE: Npc' . chr(10) ;
            }
            else
            {
                echo 'LEVEL: ' . $Obj->level . chr(10) ;
                echo 'HP: ' . $Obj->hp . chr(10) ;
                echo 'TYPE: ' ;
                # (!empty($Obj->atttype))?$Obj->atttype:'Npc'
                if (!empty($Obj->atttype))
                {
                    $Types = explode(',', $Obj->atttype) ;
                    for ($i = 0; $i < count($Types); $i++)
                    {
                        $Types[$i] = $Style[$Types[$i]] ;
                    }
                    echo implode(', ', $Types) . chr(10) ;
                }
                else
                {
                    echo 'Npc' . chr(10) ;
                }
                echo 'AGGRESSIVE: ' . (($Obj->aggressive == 1) ? 'Yes' : 'No') . chr(10) ;
                echo 'TACTICS: ' . ((!empty($Obj->tactics)) ? htmlfree($Obj->tactics) : 'None') . chr(10) ;
                echo 'DROPS: ' . ((!empty($Obj->drops)) ? $Obj->drops : 'None') . chr(10) ;
                echo 'TOPDROPS: ' . ((!empty($Obj->top_drops)) ? $Obj->top_drops : 'None') . chr(10) ;
            }                        
            echo 'EXAMINE: ' . ((!empty($Obj->examine)) ? $Obj->examine : 'None') . chr(10) ;
            echo 'LOCATION: ' . ((!empty($Obj->location)) ? $Obj->location : 'None') . chr(10) ;
        }
        $Dbc->sql_freeresult($Result) ;
    }
}
