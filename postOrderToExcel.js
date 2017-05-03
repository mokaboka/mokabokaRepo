/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Constant For Hair 
var HairStyleConatant = [];
HairStyleConatant['h1'] = 'ponytail';
HairStyleConatant['h2'] = 'Straight';
HairStyleConatant['h3'] = 'bob';
HairStyleConatant['h4'] = 'short';
HairStyleConatant['h5'] = 'shaggy';
HairStyleConatant['h6'] = 'curly';
HairStyleConatant['h7'] = 'messy';
HairStyleConatant['h8'] = 'braids';
HairStyleConatant['h9'] = 'parted';
HairStyleConatant['h10'] = 'buzzcut';
HairStyleConatant['h11'] = 'blad';

//////////////////////////

// Constant For Hair Color 
var HairColor = [];
HairColor['c1'] = 'black';
HairColor['c2'] = 'brown';
HairColor['c3'] = 'red';
HairColor['c4'] = 'blonde';

/////////////////////////
//Constant For SkinTone
var SkinToneColor = [];
SkinToneColor['sl'] = 'light';
SkinToneColor['sm'] = 'medium';
SkinToneColor['sd'] = 'dark';

////
var FavorColor = [];
FavorColor['0'] = 'White';
FavorColor['1'] = 'Yellow';
FavorColor['2'] = 'Light Green';
FavorColor['3'] = 'Dark Green';
FavorColor['4'] = 'Blue';
FavorColor['5'] = 'Navy';
FavorColor['6'] = 'Purble';
FavorColor['7'] = 'Pink';
FavorColor['8'] = 'Red';
FavorColor['9'] = 'Orange';
FavorColor['10'] = 'Brown';
FavorColor['11'] = 'Black';


var REMOTE_HOST = "http://192.254.223.36/~mokaboka/storeOrderIntoSheet.php"      
       
        

(function($){
    $('#postToExcel').on("click",function(){
         var postDataToExcel = [];
        
postDataToExcel["Firstname"] = $('#firstnamefield').val();
postDataToExcel["Lastname"] = $('#lastnamefield').val();
postDataToExcel["email"] = $('#emailfield').val();
postDataToExcel["Child1Name"] = $('#child1_firstname').val();
postDataToExcel["Child1Gender"]= $('#child1_gender').val();
postDataToExcel["Child1HairStyle"]= HairStyleConatant[$('#child1_hairstyle').val()];
postDataToExcel["Child1HairColor"]= HairColor[$('#child1_haircolor').val()];
postDataToExcel["Child1SkinTone"]= SkinToneColor[$('#child1_skintone').val()];
postDataToExcel["Child1Favoritecolor"]= FavorColor[$('#child1_favcolor').val()];
postDataToExcel["Child1to2"]= $('#child1_relationship').val();
postDataToExcel["Child2Name"]= $('#child2_firstname').val();
postDataToExcel["Child2Gender"]= $('#child2_gender').val();
postDataToExcel["Child2HairColor"]= HairColor[$('#child2_haircolor').val()];
postDataToExcel["Child2HairStyle"]= HairStyleConatant[$('#child2_hairstyle').val()];;
postDataToExcel["Child2SkinTone"]= SkinToneColor[$('#child2_skintone').val()];
postDataToExcel["Child2Favoritecolor"]= FavorColor[$('#child2_favcolor').val()];
postDataToExcel["Child2to1"]= $('#child2_relationship').val();
postDataToExcel["Image"]= "";
postDataToExcel["Dedication"]= "";

   $.ajax({type: "POST",
                url: REMOTE_HOST,
                data: JSON.stringify(postDataToExcel),
                dataType: 'application/json',
                success: function (data) {
                 console.log(data)  
                alert(data);
                }
            });
        
        
    })
    
});