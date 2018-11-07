<?php
// 地理資訊
class Geography
{
	//城市
	static function getArea()
	{
		$array[0]['1'] = array('name'=>'基隆市', 'sort'=>1);
		$array[0]['2'] = array('name'=>'台北市', 'sort'=>2);
		$array[0]['3'] = array('name'=>'新北市', 'sort'=>3);
		$array[0]['4'] = array('name'=>'宜蘭縣', 'sort'=>4);
		$array[0]['5'] = array('name'=>'新竹市', 'sort'=>5);
		$array[0]['6'] = array('name'=>'新竹縣', 'sort'=>6);
		$array[0]['7'] = array('name'=>'桃園市', 'sort'=>7);
		$array[0]['8'] = array('name'=>'苗栗縣', 'sort'=>8);
		$array[0]['9'] = array('name'=>'台中市', 'sort'=>9);
		$array[0]['10'] = array('name'=>'彰化縣', 'sort'=>10);
		$array[0]['11'] = array('name'=>'南投縣', 'sort'=>11);
		$array[0]['12'] = array('name'=>'嘉義市', 'sort'=>12);
		$array[0]['13'] = array('name'=>'嘉義縣', 'sort'=>13);
		$array[0]['14'] = array('name'=>'雲林縣', 'sort'=>14);
		$array[0]['15'] = array('name'=>'台南市', 'sort'=>15);
		$array[0]['16'] = array('name'=>'高雄市', 'sort'=>16);
		$array[0]['17'] = array('name'=>'屏東縣', 'sort'=>17);
		$array[0]['18'] = array('name'=>'台東縣', 'sort'=>18);
		$array[0]['19'] = array('name'=>'花蓮縣', 'sort'=>19);
		$array[0]['20'] = array('name'=>'金門縣', 'sort'=>20);
		$array[0]['21'] = array('name'=>'連江縣', 'sort'=>21);
		$array[0]['22'] = array('name'=>'澎湖縣', 'sort'=>22);
		$array[0]['23'] = array('name'=>'南海諸島', 'sort'=>23);
		
		return $array[0];
	}
    
    
    //地區
    static function getZip()
    {
        $array[0]   = array('city'=>'', 'area'=>'', 'sort'=>1);
        
        $array[200] = array('city'=>'基隆市', 'area'=>'仁愛區', 'sort'=>1);
        $array[201] = array('city'=>'基隆市', 'area'=>'信義區', 'sort'=>2);
        $array[202] = array('city'=>'基隆市', 'area'=>'中正區', 'sort'=>3);
        $array[203] = array('city'=>'基隆市', 'area'=>'中山區', 'sort'=>4);
        $array[204] = array('city'=>'基隆市', 'area'=>'安樂區', 'sort'=>5);
        $array[205] = array('city'=>'基隆市', 'area'=>'暖暖區', 'sort'=>6);
        $array[206] = array('city'=>'基隆市', 'area'=>'七堵區', 'sort'=>7);
        
        $array[100] = array('city'=>'台北市', 'area'=>'中正區', 'sort'=>1);
        $array[103] = array('city'=>'台北市', 'area'=>'大同區', 'sort'=>2);
        $array[104] = array('city'=>'台北市', 'area'=>'中山區', 'sort'=>3);
        $array[105] = array('city'=>'台北市', 'area'=>'松山區', 'sort'=>4);
        $array[106] = array('city'=>'台北市', 'area'=>'大安區', 'sort'=>5);
        $array[108] = array('city'=>'台北市', 'area'=>'萬華區', 'sort'=>6);
        $array[110] = array('city'=>'台北市', 'area'=>'信義區', 'sort'=>7);
        $array[111] = array('city'=>'台北市', 'area'=>'士林區', 'sort'=>8);
        $array[112] = array('city'=>'台北市', 'area'=>'北投區', 'sort'=>9);
        $array[114] = array('city'=>'台北市', 'area'=>'內湖區', 'sort'=>10);
        $array[115] = array('city'=>'台北市', 'area'=>'南港區', 'sort'=>11);
        $array[116] = array('city'=>'台北市', 'area'=>'文山區', 'sort'=>12);
        
        $array[207] = array('city'=>'新北市', 'area'=>'萬里區', 'sort'=>1);
        $array[208] = array('city'=>'新北市', 'area'=>'金山區', 'sort'=>2);
        $array[220] = array('city'=>'新北市', 'area'=>'板橋區', 'sort'=>3);
        $array[221] = array('city'=>'新北市', 'area'=>'汐止區', 'sort'=>4);
        $array[222] = array('city'=>'新北市', 'area'=>'深坑區', 'sort'=>5);
        $array[223] = array('city'=>'新北市', 'area'=>'石碇區', 'sort'=>6);
        $array[224] = array('city'=>'新北市', 'area'=>'瑞芳區', 'sort'=>7);
        $array[226] = array('city'=>'新北市', 'area'=>'平溪區', 'sort'=>8);
        $array[227] = array('city'=>'新北市', 'area'=>'雙溪區', 'sort'=>9);
        $array[228] = array('city'=>'新北市', 'area'=>'貢寮區', 'sort'=>10);
        $array[231] = array('city'=>'新北市', 'area'=>'新店區', 'sort'=>11);
        $array[232] = array('city'=>'新北市', 'area'=>'坪林區', 'sort'=>12);
        $array[233] = array('city'=>'新北市', 'area'=>'烏來區', 'sort'=>13);
        $array[234] = array('city'=>'新北市', 'area'=>'永和區', 'sort'=>14);
        $array[235] = array('city'=>'新北市', 'area'=>'中和區', 'sort'=>15);
        $array[236] = array('city'=>'新北市', 'area'=>'土城區', 'sort'=>16);
        $array[237] = array('city'=>'新北市', 'area'=>'三峽區', 'sort'=>17);
        $array[238] = array('city'=>'新北市', 'area'=>'樹林區', 'sort'=>18);
        $array[239] = array('city'=>'新北市', 'area'=>'鶯歌區', 'sort'=>19);
        $array[241] = array('city'=>'新北市', 'area'=>'三重區', 'sort'=>20);
        $array[242] = array('city'=>'新北市', 'area'=>'新莊區', 'sort'=>21);
        $array[243] = array('city'=>'新北市', 'area'=>'泰山區', 'sort'=>22);
        $array[244] = array('city'=>'新北市', 'area'=>'林口區', 'sort'=>23);
        $array[247] = array('city'=>'新北市', 'area'=>'蘆洲區', 'sort'=>24);
        $array[248] = array('city'=>'新北市', 'area'=>'五股區', 'sort'=>25);
        $array[249] = array('city'=>'新北市', 'area'=>'八里區', 'sort'=>26);
        $array[251] = array('city'=>'新北市', 'area'=>'淡水區', 'sort'=>27);
        $array[252] = array('city'=>'新北市', 'area'=>'三芝區', 'sort'=>28);
        $array[253] = array('city'=>'新北市', 'area'=>'石門區', 'sort'=>29);
        
        $array[260] = array('city'=>'宜蘭縣', 'area'=>'宜蘭市', 'sort'=>1); 
        $array[261] = array('city'=>'宜蘭縣', 'area'=>'頭城鎮', 'sort'=>2); 
        $array[262] = array('city'=>'宜蘭縣', 'area'=>'礁溪鄉', 'sort'=>3); 
        $array[263] = array('city'=>'宜蘭縣', 'area'=>'壯圍鄉', 'sort'=>4); 
        $array[264] = array('city'=>'宜蘭縣', 'area'=>'員山鄉', 'sort'=>5); 
        $array[265] = array('city'=>'宜蘭縣', 'area'=>'羅東鎮', 'sort'=>6); 
        $array[266] = array('city'=>'宜蘭縣', 'area'=>'三星鄉', 'sort'=>7); 
        $array[267] = array('city'=>'宜蘭縣', 'area'=>'大同鄉', 'sort'=>8); 
        $array[268] = array('city'=>'宜蘭縣', 'area'=>'五結鄉', 'sort'=>9); 
        $array[269] = array('city'=>'宜蘭縣', 'area'=>'冬山鄉', 'sort'=>10); 
        $array[270] = array('city'=>'宜蘭縣', 'area'=>'蘇澳鎮', 'sort'=>11); 
        $array[272] = array('city'=>'宜蘭縣', 'area'=>'南澳鄉', 'sort'=>12); 
        $array[290] = array('city'=>'宜蘭縣', 'area'=>'釣魚台列嶼', 'sort'=>13);
        
        $array[300] = array('city'=>'新竹市', 'area'=>'東區', 'sort'=>1); 
        $array[300] = array('city'=>'新竹市', 'area'=>'北區', 'sort'=>2); 
        $array[300] = array('city'=>'新竹市', 'area'=>'香山區', 'sort'=>3); 

        $array[302] = array('city'=>'新竹縣', 'area'=>'竹北市', 'sort'=>1); 
        $array[303] = array('city'=>'新竹縣', 'area'=>'湖口鄉', 'sort'=>2); 
        $array[304] = array('city'=>'新竹縣', 'area'=>'新豐鄉', 'sort'=>3); 
        $array[305] = array('city'=>'新竹縣', 'area'=>'新埔鎮', 'sort'=>4); 
        $array[306] = array('city'=>'新竹縣', 'area'=>'關西鎮', 'sort'=>5); 
        $array[307] = array('city'=>'新竹縣', 'area'=>'芎林鄉', 'sort'=>6); 
        $array[308] = array('city'=>'新竹縣', 'area'=>'寶山鄉', 'sort'=>7); 
        $array[310] = array('city'=>'新竹縣', 'area'=>'竹東鎮', 'sort'=>8); 
        $array[311] = array('city'=>'新竹縣', 'area'=>'五峰鄉', 'sort'=>9); 
        $array[312] = array('city'=>'新竹縣', 'area'=>'橫山鄉', 'sort'=>10); 
        $array[313] = array('city'=>'新竹縣', 'area'=>'尖石鄉', 'sort'=>11); 
        $array[314] = array('city'=>'新竹縣', 'area'=>'北埔鄉', 'sort'=>12); 
        $array[315] = array('city'=>'新竹縣', 'area'=>'峨嵋鄉', 'sort'=>13); 
        
        $array[320] = array('city'=>'桃園市', 'area'=>'中壢區', 'sort'=>1); 
        $array[324] = array('city'=>'桃園市', 'area'=>'平鎮區', 'sort'=>2); 
        $array[325] = array('city'=>'桃園市', 'area'=>'龍潭區', 'sort'=>3); 
        $array[326] = array('city'=>'桃園市', 'area'=>'楊梅區', 'sort'=>4); 
        $array[327] = array('city'=>'桃園市', 'area'=>'新屋區', 'sort'=>5); 
        $array[328] = array('city'=>'桃園市', 'area'=>'觀音區', 'sort'=>6); 
        $array[330] = array('city'=>'桃園市', 'area'=>'桃園市', 'sort'=>7); 
        $array[333] = array('city'=>'桃園市', 'area'=>'龜山區', 'sort'=>8); 
        $array[334] = array('city'=>'桃園市', 'area'=>'八德區', 'sort'=>9); 
        $array[335] = array('city'=>'桃園市', 'area'=>'大溪區', 'sort'=>10); 
        $array[336] = array('city'=>'桃園市', 'area'=>'復興區', 'sort'=>11); 
        $array[337] = array('city'=>'桃園市', 'area'=>'大園區', 'sort'=>12); 
        $array[338] = array('city'=>'桃園市', 'area'=>'蘆竹區', 'sort'=>13);
        
        $array[350] = array('city'=>'苗栗縣', 'area'=>'竹南鎮', 'sort'=>1); 
        $array[351] = array('city'=>'苗栗縣', 'area'=>'頭份鎮', 'sort'=>2); 
        $array[352] = array('city'=>'苗栗縣', 'area'=>'三灣鄉', 'sort'=>3); 
        $array[353] = array('city'=>'苗栗縣', 'area'=>'南庄鄉', 'sort'=>4); 
        $array[354] = array('city'=>'苗栗縣', 'area'=>'獅潭鄉', 'sort'=>5); 
        $array[356] = array('city'=>'苗栗縣', 'area'=>'後龍鎮', 'sort'=>6); 
        $array[357] = array('city'=>'苗栗縣', 'area'=>'通霄鎮', 'sort'=>7); 
        $array[358] = array('city'=>'苗栗縣', 'area'=>'苑裡鎮', 'sort'=>8); 
        $array[360] = array('city'=>'苗栗縣', 'area'=>'苗栗市', 'sort'=>9); 
        $array[361] = array('city'=>'苗栗縣', 'area'=>'造橋鄉', 'sort'=>10); 
        $array[362] = array('city'=>'苗栗縣', 'area'=>'頭屋鄉', 'sort'=>11); 
        $array[363] = array('city'=>'苗栗縣', 'area'=>'公館鄉', 'sort'=>12); 
        $array[364] = array('city'=>'苗栗縣', 'area'=>'大湖鄉', 'sort'=>13); 
        $array[365] = array('city'=>'苗栗縣', 'area'=>'泰安鄉', 'sort'=>14); 
        $array[366] = array('city'=>'苗栗縣', 'area'=>'銅鑼鄉', 'sort'=>15); 
        $array[367] = array('city'=>'苗栗縣', 'area'=>'三義鄉', 'sort'=>16); 
        $array[368] = array('city'=>'苗栗縣', 'area'=>'西湖鄉', 'sort'=>17); 
        $array[369] = array('city'=>'苗栗縣', 'area'=>'卓蘭鎮', 'sort'=>18);
        
        $array[400] = array('city'=>'台中市', 'area'=>'中區', 'sort'=>1); 
        $array[401] = array('city'=>'台中市', 'area'=>'東區', 'sort'=>2); 
        $array[402] = array('city'=>'台中市', 'area'=>'南區', 'sort'=>3); 
        $array[403] = array('city'=>'台中市', 'area'=>'西區', 'sort'=>4); 
        $array[404] = array('city'=>'台中市', 'area'=>'北區', 'sort'=>5); 
        $array[406] = array('city'=>'台中市', 'area'=>'北屯區', 'sort'=>6); 
        $array[407] = array('city'=>'台中市', 'area'=>'西屯區', 'sort'=>7); 
        $array[408] = array('city'=>'台中市', 'area'=>'南屯區', 'sort'=>8); 
        $array[411] = array('city'=>'台中市', 'area'=>'太平區', 'sort'=>9); 
        $array[412] = array('city'=>'台中市', 'area'=>'大里區', 'sort'=>10); 
        $array[413] = array('city'=>'台中市', 'area'=>'霧峰區', 'sort'=>11); 
        $array[414] = array('city'=>'台中市', 'area'=>'烏日區', 'sort'=>12); 
        $array[420] = array('city'=>'台中市', 'area'=>'豐原區', 'sort'=>13); 
        $array[421] = array('city'=>'台中市', 'area'=>'后里區', 'sort'=>14); 
        $array[422] = array('city'=>'台中市', 'area'=>'石岡區', 'sort'=>15); 
        $array[423] = array('city'=>'台中市', 'area'=>'東勢區', 'sort'=>16); 
        $array[424] = array('city'=>'台中市', 'area'=>'和平區', 'sort'=>17); 
        $array[426] = array('city'=>'台中市', 'area'=>'新社區', 'sort'=>18); 
        $array[427] = array('city'=>'台中市', 'area'=>'潭子區', 'sort'=>19); 
        $array[428] = array('city'=>'台中市', 'area'=>'大雅區', 'sort'=>20); 
        $array[429] = array('city'=>'台中市', 'area'=>'神岡區', 'sort'=>21); 
        $array[432] = array('city'=>'台中市', 'area'=>'大肚區', 'sort'=>22); 
        $array[433] = array('city'=>'台中市', 'area'=>'沙鹿區', 'sort'=>23); 
        $array[434] = array('city'=>'台中市', 'area'=>'龍井區', 'sort'=>24); 
        $array[435] = array('city'=>'台中市', 'area'=>'梧棲區', 'sort'=>25); 
        $array[436] = array('city'=>'台中市', 'area'=>'清水區', 'sort'=>26); 
        $array[437] = array('city'=>'台中市', 'area'=>'大甲區', 'sort'=>27); 
        $array[438] = array('city'=>'台中市', 'area'=>'外埔區', 'sort'=>28); 
        $array[439] = array('city'=>'台中市', 'area'=>'大安區', 'sort'=>29);
        
        $array[500] = array('city'=>'彰化縣', 'area'=>'彰化市', 'sort'=>1); 
        $array[502] = array('city'=>'彰化縣', 'area'=>'芬園鄉', 'sort'=>2); 
        $array[503] = array('city'=>'彰化縣', 'area'=>'花壇鄉', 'sort'=>3); 
        $array[504] = array('city'=>'彰化縣', 'area'=>'秀水鄉', 'sort'=>4); 
        $array[505] = array('city'=>'彰化縣', 'area'=>'鹿港鎮', 'sort'=>5); 
        $array[506] = array('city'=>'彰化縣', 'area'=>'福興鄉', 'sort'=>6); 
        $array[507] = array('city'=>'彰化縣', 'area'=>'線西鄉', 'sort'=>7); 
        $array[508] = array('city'=>'彰化縣', 'area'=>'和美鎮', 'sort'=>8); 
        $array[509] = array('city'=>'彰化縣', 'area'=>'伸港鄉', 'sort'=>9); 
        $array[510] = array('city'=>'彰化縣', 'area'=>'員林鎮', 'sort'=>10); 
        $array[511] = array('city'=>'彰化縣', 'area'=>'社頭鄉', 'sort'=>11); 
        $array[512] = array('city'=>'彰化縣', 'area'=>'永靖鄉', 'sort'=>12); 
        $array[513] = array('city'=>'彰化縣', 'area'=>'埔心鄉', 'sort'=>13); 
        $array[514] = array('city'=>'彰化縣', 'area'=>'溪湖鎮', 'sort'=>14); 
        $array[515] = array('city'=>'彰化縣', 'area'=>'大村鄉', 'sort'=>15); 
        $array[516] = array('city'=>'彰化縣', 'area'=>'埔鹽鄉', 'sort'=>16); 
        $array[520] = array('city'=>'彰化縣', 'area'=>'田中鎮', 'sort'=>17); 
        $array[521] = array('city'=>'彰化縣', 'area'=>'北斗鎮', 'sort'=>18); 
        $array[522] = array('city'=>'彰化縣', 'area'=>'田尾鄉', 'sort'=>19); 
        $array[523] = array('city'=>'彰化縣', 'area'=>'埤頭鄉', 'sort'=>20); 
        $array[524] = array('city'=>'彰化縣', 'area'=>'溪州鄉', 'sort'=>21); 
        $array[525] = array('city'=>'彰化縣', 'area'=>'竹塘鄉', 'sort'=>22); 
        $array[526] = array('city'=>'彰化縣', 'area'=>'二林鎮', 'sort'=>23); 
        $array[527] = array('city'=>'彰化縣', 'area'=>'大城鄉', 'sort'=>24); 
        $array[528] = array('city'=>'彰化縣', 'area'=>'芳苑鄉', 'sort'=>25); 
        $array[530] = array('city'=>'彰化縣', 'area'=>'二水鄉', 'sort'=>26);        
 
        $array[540] = array('city'=>'南投縣', 'area'=>'南投市', 'sort'=>1); 
        $array[541] = array('city'=>'南投縣', 'area'=>'中寮鄉', 'sort'=>2); 
        $array[542] = array('city'=>'南投縣', 'area'=>'草屯鎮', 'sort'=>3); 
        $array[544] = array('city'=>'南投縣', 'area'=>'國姓鄉', 'sort'=>4); 
        $array[545] = array('city'=>'南投縣', 'area'=>'埔里鎮', 'sort'=>5); 
        $array[546] = array('city'=>'南投縣', 'area'=>'仁愛鄉', 'sort'=>6); 
        $array[551] = array('city'=>'南投縣', 'area'=>'名間鄉', 'sort'=>7); 
        $array[552] = array('city'=>'南投縣', 'area'=>'集集鎮', 'sort'=>8); 
        $array[553] = array('city'=>'南投縣', 'area'=>'水里鄉', 'sort'=>9); 
        $array[555] = array('city'=>'南投縣', 'area'=>'魚池鄉', 'sort'=>10); 
        $array[556] = array('city'=>'南投縣', 'area'=>'信義鄉', 'sort'=>11); 
        $array[557] = array('city'=>'南投縣', 'area'=>'竹山鎮', 'sort'=>12); 
        $array[558] = array('city'=>'南投縣', 'area'=>'鹿谷鄉', 'sort'=>13); 

        $array[600] = array('city'=>'嘉義市', 'area'=>'東區', 'sort'=>1); 
        $array[600] = array('city'=>'嘉義市', 'area'=>'西區', 'sort'=>2); 

        $array[602] = array('city'=>'嘉義縣', 'area'=>'番路鄉', 'sort'=>1); 
        $array[603] = array('city'=>'嘉義縣', 'area'=>'梅山鄉', 'sort'=>2); 
        $array[604] = array('city'=>'嘉義縣', 'area'=>'竹崎鄉', 'sort'=>3); 
        $array[605] = array('city'=>'嘉義縣', 'area'=>'阿里山', 'sort'=>4); 
        $array[606] = array('city'=>'嘉義縣', 'area'=>'中埔鄉', 'sort'=>5); 
        $array[607] = array('city'=>'嘉義縣', 'area'=>'大埔鄉', 'sort'=>6); 
        $array[608] = array('city'=>'嘉義縣', 'area'=>'水上鄉', 'sort'=>7); 
        $array[611] = array('city'=>'嘉義縣', 'area'=>'鹿草鄉', 'sort'=>8); 
        $array[612] = array('city'=>'嘉義縣', 'area'=>'太保市', 'sort'=>9); 
        $array[613] = array('city'=>'嘉義縣', 'area'=>'朴子市', 'sort'=>10); 
        $array[614] = array('city'=>'嘉義縣', 'area'=>'東石鄉', 'sort'=>11); 
        $array[615] = array('city'=>'嘉義縣', 'area'=>'六腳鄉', 'sort'=>12); 
        $array[616] = array('city'=>'嘉義縣', 'area'=>'新港鄉', 'sort'=>13); 
        $array[621] = array('city'=>'嘉義縣', 'area'=>'民雄鄉', 'sort'=>14); 
        $array[622] = array('city'=>'嘉義縣', 'area'=>'大林鎮', 'sort'=>15); 
        $array[623] = array('city'=>'嘉義縣', 'area'=>'溪口鄉', 'sort'=>16); 
        $array[624] = array('city'=>'嘉義縣', 'area'=>'義竹鄉', 'sort'=>17); 
        $array[625] = array('city'=>'嘉義縣', 'area'=>'布袋鎮', 'sort'=>18);

        $array[630] = array('city'=>'雲林縣', 'area'=>'斗南鎮', 'sort'=>1); 
        $array[631] = array('city'=>'雲林縣', 'area'=>'大埤鄉', 'sort'=>2); 
        $array[632] = array('city'=>'雲林縣', 'area'=>'虎尾鎮', 'sort'=>3); 
        $array[633] = array('city'=>'雲林縣', 'area'=>'土庫鎮', 'sort'=>4); 
        $array[634] = array('city'=>'雲林縣', 'area'=>'褒忠鄉', 'sort'=>5); 
        $array[635] = array('city'=>'雲林縣', 'area'=>'東勢鄉', 'sort'=>6); 
        $array[636] = array('city'=>'雲林縣', 'area'=>'臺西鄉', 'sort'=>7); 
        $array[637] = array('city'=>'雲林縣', 'area'=>'崙背鄉', 'sort'=>8); 
        $array[638] = array('city'=>'雲林縣', 'area'=>'麥寮鄉', 'sort'=>9); 
        $array[640] = array('city'=>'雲林縣', 'area'=>'斗六市', 'sort'=>10); 
        $array[643] = array('city'=>'雲林縣', 'area'=>'林內鄉', 'sort'=>11); 
        $array[646] = array('city'=>'雲林縣', 'area'=>'古坑鄉', 'sort'=>12); 
        $array[647] = array('city'=>'雲林縣', 'area'=>'莿桐鄉', 'sort'=>13); 
        $array[648] = array('city'=>'雲林縣', 'area'=>'西螺鎮', 'sort'=>14); 
        $array[649] = array('city'=>'雲林縣', 'area'=>'二崙鄉', 'sort'=>15); 
        $array[651] = array('city'=>'雲林縣', 'area'=>'北港鎮', 'sort'=>16); 
        $array[652] = array('city'=>'雲林縣', 'area'=>'水林鄉', 'sort'=>17); 
        $array[653] = array('city'=>'雲林縣', 'area'=>'口湖鄉', 'sort'=>18); 
        $array[654] = array('city'=>'雲林縣', 'area'=>'四湖鄉', 'sort'=>19); 
        $array[655] = array('city'=>'雲林縣', 'area'=>'元長鄉', 'sort'=>20); 

        $array[700] = array('city'=>'台南市', 'area'=>'中西區', 'sort'=>1); 
        $array[701] = array('city'=>'台南市', 'area'=>'東區', 'sort'=>2); 
        $array[702] = array('city'=>'台南市', 'area'=>'南區', 'sort'=>3); 
        $array[704] = array('city'=>'台南市', 'area'=>'北區', 'sort'=>4); 
        $array[708] = array('city'=>'台南市', 'area'=>'安平區', 'sort'=>5); 
        $array[709] = array('city'=>'台南市', 'area'=>'安南區', 'sort'=>6); 
        $array[710] = array('city'=>'台南市', 'area'=>'永康區', 'sort'=>7); 
        $array[711] = array('city'=>'台南市', 'area'=>'歸仁區', 'sort'=>8); 
        $array[712] = array('city'=>'台南市', 'area'=>'新化區', 'sort'=>9); 
        $array[713] = array('city'=>'台南市', 'area'=>'左鎮區', 'sort'=>10); 
        $array[714] = array('city'=>'台南市', 'area'=>'玉井區', 'sort'=>11); 
        $array[715] = array('city'=>'台南市', 'area'=>'楠西區', 'sort'=>12); 
        $array[716] = array('city'=>'台南市', 'area'=>'南化區', 'sort'=>13); 
        $array[717] = array('city'=>'台南市', 'area'=>'仁德區', 'sort'=>14); 
        $array[718] = array('city'=>'台南市', 'area'=>'關廟區', 'sort'=>15); 
        $array[719] = array('city'=>'台南市', 'area'=>'龍崎區', 'sort'=>16); 
        $array[720] = array('city'=>'台南市', 'area'=>'官田區', 'sort'=>17); 
        $array[721] = array('city'=>'台南市', 'area'=>'麻豆區', 'sort'=>18); 
        $array[722] = array('city'=>'台南市', 'area'=>'佳里區', 'sort'=>19); 
        $array[723] = array('city'=>'台南市', 'area'=>'西港區', 'sort'=>20); 
        $array[724] = array('city'=>'台南市', 'area'=>'七股區', 'sort'=>21); 
        $array[725] = array('city'=>'台南市', 'area'=>'將軍區', 'sort'=>22); 
        $array[726] = array('city'=>'台南市', 'area'=>'學甲區', 'sort'=>23); 
        $array[727] = array('city'=>'台南市', 'area'=>'北門區', 'sort'=>24); 
        $array[730] = array('city'=>'台南市', 'area'=>'新營區', 'sort'=>25); 
        $array[731] = array('city'=>'台南市', 'area'=>'後壁區', 'sort'=>26); 
        $array[732] = array('city'=>'台南市', 'area'=>'白河區', 'sort'=>27); 
        $array[733] = array('city'=>'台南市', 'area'=>'東山區', 'sort'=>28); 
        $array[734] = array('city'=>'台南市', 'area'=>'六甲區', 'sort'=>29); 
        $array[735] = array('city'=>'台南市', 'area'=>'下營區', 'sort'=>30); 
        $array[736] = array('city'=>'台南市', 'area'=>'柳營區', 'sort'=>31); 
        $array[737] = array('city'=>'台南市', 'area'=>'鹽水區', 'sort'=>32); 
        $array[741] = array('city'=>'台南市', 'area'=>'善化區', 'sort'=>33); 
        $array[742] = array('city'=>'台南市', 'area'=>'大內區', 'sort'=>34); 
        $array[743] = array('city'=>'台南市', 'area'=>'山上區', 'sort'=>35); 
        $array[744] = array('city'=>'台南市', 'area'=>'新市區', 'sort'=>36); 
        $array[745] = array('city'=>'台南市', 'area'=>'安定區', 'sort'=>37);

        $array[800] = array('city'=>'高雄市', 'area'=>'新興區', 'sort'=>1); 
        $array[801] = array('city'=>'高雄市', 'area'=>'前金區', 'sort'=>2); 
        $array[802] = array('city'=>'高雄市', 'area'=>'苓雅區', 'sort'=>3); 
        $array[803] = array('city'=>'高雄市', 'area'=>'鹽埕區', 'sort'=>4); 
        $array[804] = array('city'=>'高雄市', 'area'=>'鼓山區', 'sort'=>5); 
        $array[805] = array('city'=>'高雄市', 'area'=>'旗津區', 'sort'=>6); 
        $array[806] = array('city'=>'高雄市', 'area'=>'前鎮區', 'sort'=>7); 
        $array[807] = array('city'=>'高雄市', 'area'=>'三民區', 'sort'=>8); 
        $array[811] = array('city'=>'高雄市', 'area'=>'楠梓區', 'sort'=>9); 
        $array[812] = array('city'=>'高雄市', 'area'=>'小港區', 'sort'=>10); 
        $array[813] = array('city'=>'高雄市', 'area'=>'左營區', 'sort'=>11); 
        $array[814] = array('city'=>'高雄市', 'area'=>'仁武區', 'sort'=>12); 
        $array[815] = array('city'=>'高雄市', 'area'=>'大社區', 'sort'=>13); 
        $array[820] = array('city'=>'高雄市', 'area'=>'岡山區', 'sort'=>14); 
        $array[821] = array('city'=>'高雄市', 'area'=>'路竹區', 'sort'=>15); 
        $array[822] = array('city'=>'高雄市', 'area'=>'阿蓮區', 'sort'=>16); 
        $array[823] = array('city'=>'高雄市', 'area'=>'田寮鄉', 'sort'=>17); 
        $array[824] = array('city'=>'高雄市', 'area'=>'燕巢區', 'sort'=>18); 
        $array[825] = array('city'=>'高雄市', 'area'=>'橋頭區', 'sort'=>19); 
        $array[826] = array('city'=>'高雄市', 'area'=>'梓官區', 'sort'=>20); 
        $array[827] = array('city'=>'高雄市', 'area'=>'彌陀區', 'sort'=>21); 
        $array[828] = array('city'=>'高雄市', 'area'=>'永安區', 'sort'=>22); 
        $array[829] = array('city'=>'高雄市', 'area'=>'湖內鄉', 'sort'=>23); 
        $array[830] = array('city'=>'高雄市', 'area'=>'鳳山區', 'sort'=>24); 
        $array[831] = array('city'=>'高雄市', 'area'=>'大寮區', 'sort'=>25); 
        $array[832] = array('city'=>'高雄市', 'area'=>'林園區', 'sort'=>26); 
        $array[833] = array('city'=>'高雄市', 'area'=>'鳥松區', 'sort'=>27); 
        $array[840] = array('city'=>'高雄市', 'area'=>'大樹區', 'sort'=>28); 
        $array[842] = array('city'=>'高雄市', 'area'=>'旗山區', 'sort'=>29); 
        $array[843] = array('city'=>'高雄市', 'area'=>'美濃區', 'sort'=>30); 
        $array[844] = array('city'=>'高雄市', 'area'=>'六龜區', 'sort'=>31); 
        $array[845] = array('city'=>'高雄市', 'area'=>'內門區', 'sort'=>32); 
        $array[846] = array('city'=>'高雄市', 'area'=>'杉林區', 'sort'=>33); 
        $array[847] = array('city'=>'高雄市', 'area'=>'甲仙區', 'sort'=>34); 
        $array[848] = array('city'=>'高雄市', 'area'=>'桃源區', 'sort'=>35); 
        $array[849] = array('city'=>'高雄市', 'area'=>'那瑪夏區', 'sort'=>36); 
        $array[851] = array('city'=>'高雄市', 'area'=>'茂林區', 'sort'=>37); 
        $array[852] = array('city'=>'高雄市', 'area'=>'茄萣區', 'sort'=>38);

        $array[900] = array('city'=>'屏東縣', 'area'=>'屏東市', 'sort'=>1); 
        $array[901] = array('city'=>'屏東縣', 'area'=>'三地門', 'sort'=>2); 
        $array[902] = array('city'=>'屏東縣', 'area'=>'霧臺鄉', 'sort'=>3); 
        $array[903] = array('city'=>'屏東縣', 'area'=>'瑪家鄉', 'sort'=>4); 
        $array[904] = array('city'=>'屏東縣', 'area'=>'九如鄉', 'sort'=>5); 
        $array[905] = array('city'=>'屏東縣', 'area'=>'里港鄉', 'sort'=>6); 
        $array[906] = array('city'=>'屏東縣', 'area'=>'高樹鄉', 'sort'=>7); 
        $array[907] = array('city'=>'屏東縣', 'area'=>'鹽埔鄉', 'sort'=>8); 
        $array[908] = array('city'=>'屏東縣', 'area'=>'長治鄉', 'sort'=>9); 
        $array[909] = array('city'=>'屏東縣', 'area'=>'麟洛鄉', 'sort'=>10); 
        $array[911] = array('city'=>'屏東縣', 'area'=>'竹田鄉', 'sort'=>11); 
        $array[912] = array('city'=>'屏東縣', 'area'=>'內埔鄉', 'sort'=>12); 
        $array[913] = array('city'=>'屏東縣', 'area'=>'萬丹鄉', 'sort'=>13); 
        $array[920] = array('city'=>'屏東縣', 'area'=>'潮州鎮', 'sort'=>14); 
        $array[921] = array('city'=>'屏東縣', 'area'=>'泰武鄉', 'sort'=>15); 
        $array[922] = array('city'=>'屏東縣', 'area'=>'來義鄉', 'sort'=>16); 
        $array[923] = array('city'=>'屏東縣', 'area'=>'萬巒鄉', 'sort'=>17); 
        $array[924] = array('city'=>'屏東縣', 'area'=>'崁頂鄉', 'sort'=>18); 
        $array[925] = array('city'=>'屏東縣', 'area'=>'新埤鄉', 'sort'=>19); 
        $array[926] = array('city'=>'屏東縣', 'area'=>'南州鄉', 'sort'=>20); 
        $array[927] = array('city'=>'屏東縣', 'area'=>'林邊鄉', 'sort'=>21); 
        $array[928] = array('city'=>'屏東縣', 'area'=>'東港鎮', 'sort'=>22); 
        $array[929] = array('city'=>'屏東縣', 'area'=>'琉球鄉', 'sort'=>23); 
        $array[931] = array('city'=>'屏東縣', 'area'=>'佳冬鄉', 'sort'=>24); 
        $array[932] = array('city'=>'屏東縣', 'area'=>'新園鄉', 'sort'=>25); 
        $array[940] = array('city'=>'屏東縣', 'area'=>'枋寮鄉', 'sort'=>26); 
        $array[941] = array('city'=>'屏東縣', 'area'=>'枋山鄉', 'sort'=>27); 
        $array[942] = array('city'=>'屏東縣', 'area'=>'春日鄉', 'sort'=>28); 
        $array[943] = array('city'=>'屏東縣', 'area'=>'獅子鄉', 'sort'=>29); 
        $array[944] = array('city'=>'屏東縣', 'area'=>'車城鄉', 'sort'=>30); 
        $array[945] = array('city'=>'屏東縣', 'area'=>'牡丹鄉', 'sort'=>31); 
        $array[946] = array('city'=>'屏東縣', 'area'=>'恆春鎮', 'sort'=>32); 
        $array[947] = array('city'=>'屏東縣', 'area'=>'滿州鄉', 'sort'=>33);

        $array[950] = array('city'=>'台東縣', 'area'=>'臺東市', 'sort'=>1); 
        $array[951] = array('city'=>'台東縣', 'area'=>'綠島鄉', 'sort'=>2); 
        $array[952] = array('city'=>'台東縣', 'area'=>'蘭嶼鄉', 'sort'=>3); 
        $array[953] = array('city'=>'台東縣', 'area'=>'延平鄉', 'sort'=>4); 
        $array[954] = array('city'=>'台東縣', 'area'=>'卑南鄉', 'sort'=>5); 
        $array[955] = array('city'=>'台東縣', 'area'=>'鹿野鄉', 'sort'=>6); 
        $array[956] = array('city'=>'台東縣', 'area'=>'關山鎮', 'sort'=>7); 
        $array[957] = array('city'=>'台東縣', 'area'=>'海端鄉', 'sort'=>8); 
        $array[958] = array('city'=>'台東縣', 'area'=>'池上鄉', 'sort'=>9); 
        $array[959] = array('city'=>'台東縣', 'area'=>'東河鄉', 'sort'=>10); 
        $array[961] = array('city'=>'台東縣', 'area'=>'成功鎮', 'sort'=>11); 
        $array[962] = array('city'=>'台東縣', 'area'=>'長濱鄉', 'sort'=>12); 
        $array[963] = array('city'=>'台東縣', 'area'=>'太麻里鄉', 'sort'=>13); 
        $array[964] = array('city'=>'台東縣', 'area'=>'金峰鄉', 'sort'=>14); 
        $array[965] = array('city'=>'台東縣', 'area'=>'大武鄉', 'sort'=>15); 
        $array[966] = array('city'=>'台東縣', 'area'=>'達仁鄉', 'sort'=>16); 

        $array[970] = array('city'=>'花蓮縣', 'area'=>'花蓮市', 'sort'=>1); 
        $array[971] = array('city'=>'花蓮縣', 'area'=>'新城鄉', 'sort'=>2); 
        $array[972] = array('city'=>'花蓮縣', 'area'=>'秀林鄉', 'sort'=>3); 
        $array[973] = array('city'=>'花蓮縣', 'area'=>'吉安鄉', 'sort'=>4); 
        $array[974] = array('city'=>'花蓮縣', 'area'=>'壽豐鄉', 'sort'=>5); 
        $array[975] = array('city'=>'花蓮縣', 'area'=>'鳳林鎮', 'sort'=>6); 
        $array[976] = array('city'=>'花蓮縣', 'area'=>'光復鄉', 'sort'=>7); 
        $array[977] = array('city'=>'花蓮縣', 'area'=>'豐濱鄉', 'sort'=>8); 
        $array[978] = array('city'=>'花蓮縣', 'area'=>'瑞穗鄉', 'sort'=>9); 
        $array[979] = array('city'=>'花蓮縣', 'area'=>'萬榮鄉', 'sort'=>10); 
        $array[981] = array('city'=>'花蓮縣', 'area'=>'玉里鎮', 'sort'=>11); 
        $array[982] = array('city'=>'花蓮縣', 'area'=>'卓溪鄉', 'sort'=>12); 
        $array[983] = array('city'=>'花蓮縣', 'area'=>'富里鄉', 'sort'=>13);

        $array[890] = array('city'=>'金門縣', 'area'=>'金沙鎮', 'sort'=>1); 
        $array[891] = array('city'=>'金門縣', 'area'=>'金湖鎮', 'sort'=>2); 
        $array[892] = array('city'=>'金門縣', 'area'=>'金寧鄉', 'sort'=>3); 
        $array[893] = array('city'=>'金門縣', 'area'=>'金城鎮', 'sort'=>4); 
        $array[894] = array('city'=>'金門縣', 'area'=>'烈嶼鄉', 'sort'=>5); 
        $array[896] = array('city'=>'金門縣', 'area'=>'烏坵鄉', 'sort'=>6); 

        $array[209] = array('city'=>'連江縣', 'area'=>'南竿鄉', 'sort'=>1); 
        $array[210] = array('city'=>'連江縣', 'area'=>'北竿鄉', 'sort'=>2); 
        $array[211] = array('city'=>'連江縣', 'area'=>'莒光鄉', 'sort'=>3); 
        $array[212] = array('city'=>'連江縣', 'area'=>'東引鄉', 'sort'=>4); 

        $array[880] = array('city'=>'澎湖縣', 'area'=>'馬公市', 'sort'=>1); 
        $array[881] = array('city'=>'澎湖縣', 'area'=>'西嶼鄉', 'sort'=>2); 
        $array[882] = array('city'=>'澎湖縣', 'area'=>'望安鄉', 'sort'=>3); 
        $array[883] = array('city'=>'澎湖縣', 'area'=>'七美鄉', 'sort'=>4); 
        $array[884] = array('city'=>'澎湖縣', 'area'=>'白沙鄉', 'sort'=>5); 
        $array[885] = array('city'=>'澎湖縣', 'area'=>'湖西鄉', 'sort'=>6);

        $array[817] = array('city'=>'南海諸島', 'area'=>'東沙', 'sort'=>1); 
        $array[819] = array('city'=>'南海諸島', 'area'=>'南沙', 'sort'=>2); 

        return $array;
    }
    
    
    /**
     * 取出指定城市的所有區碼
     * @zip_array = 地區區碼陣列
     * @city = 要取出的城市名稱
     */
    static function getAreaZip($zip_array =array(), $city = null)
    {
        $i        = 0;
        $area_zip = '';
        
        if($zip_array > 0)
        {
            foreach($zip_array as $key => $value)
            {
                if($city == $value['city'])
                {
                    if($i == 0)
                    {
                        $area_zip .= $key;
                    } else {
                        $area_zip .= ', '.$key;
                    }

                    $i++;
                }
            }
        }
        
        return $area_zip;
    }
}
?>