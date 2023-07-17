<?php
/*主要選單*/
return [
   /* ["code"=>"KA0",
    	"sub_menu"=>[
    		"KA1"  =>"系所管理",
    		"KA2"  =>"組別管理",
    		
    	],
    	"sub_menu_link"=>[
    		"KA1"  =>"gradation_manager",
    		"KA2"  =>"organization_manager",
    	],
    	"name"=>"交大系所管理",
    	"link"=>"scahss"
    ],
    
    ["code"=>"KV0",
    	"sub_menu"=>[
    		"KV0"  =>"(A)表查詢",
    		"KV1"  =>"(A)表管理",
    		"KV7"  =>"(A)表列印",
    		"KV6"  =>"(B)表列印",
    		"KV2"  =>"已關閉的(A)表",
    		"KV3"  =>"新增相片",
    		"KV4"  =>"學生資料上傳",
    		
    	],
    	"sub_menu_link"=>[
    		"KV0"  =>"a_view",
    		"KV1"  =>"a_control",
    		"KV7"  =>"print_a",
    		"KV6"  =>"print_b",
    		"KV2"  =>"a_control_close",
    		"KV3"  =>"pics_insert",
    		"KV4"  =>"student_data_upload",
    	],
    	"name"=>"學生綜合資料管理",
    	"link"=>"article"
    ],*/
    
    ["code"=>"Kw0",
    	"sub_menu"=>[
    		"Kw1"  =>"維修申請單管理",
    		"Kw2"  =>"報表列印",
    		"Kw3"  =>"廠商資料管理",
    		"Kw4"  =>"維修項目管理",
    		"Kw5"  =>"學生帳號管理",
    		
    	],
    	"sub_menu_link"=>[
    		"Kw1"  =>"maintain_view",
    		"Kw2"  =>"maintain_print",
    		"Kw3"  =>"factory_setup",
    		"Kw4"  =>"category_setup",
    		"Kw5"  =>"user_setup",
    	],
    	"name"=>"宿舍修繕管理",
    	"link"=>"article"
    ],
    
    ["code"=>"KT0",
    	"sub_menu"=>[
    		"KT1"  =>"宿舍財產管理查看",
    		"KT2"  =>"宿舍財產管理新增",
    		"KT5"  =>"盤點表匯出",
    		
    	],
    	"sub_menu_link"=>[
    		"KT1"  =>"dormfortune_view",
    		"KT2"  =>"dormfortune_add",
    		"KT5"  =>"dormfortune_output",
    	],
    	"name"=>"宿舍財產管理",
    	"link"=>"article"
    ],
    ["code"=>"KS0",
    	"sub_menu"=>[
    		//"KS1"  =>"帳號新增",
    		"KS2"  =>"帳號管理",
    		//"KS6"  =>"帳號申請管理",
    		//"KS5"  =>"學生資料庫更新",
    		//"KS3"  =>"資料庫更新狀況",
    		//"KS4"  =>"資料庫備份",
    		"KS7"  =>"目前在線帳號",
    		
    	],
    	"sub_menu_link"=>[
    		//"KS1"  =>"sys_control_add",
    		"KS2"  =>"sys_control_view",
    		//"KS6"  =>"sys_control_petition",
    		"KS5"  =>"ms_sql_copy",
    		"KS3"  =>"ms_sql_copy_log",
    		"KS4"  =>"db_save_rul284",
    		"KS7"  =>"online_user",
    	],
    	"name"=>"生輔組系統管理",
    	"link"=>"article"
    ],
    
    ["code"=>"ME0",
    	"sub_menu"=>[
    		"ME2"  =>"修改個人密碼",
    		
    	],
    	"sub_menu_link"=>[
    		"ME2"  =>"manager_admin",
    	],
    	"name"=>"系統管理",
    	"link"=>"system"
    ]
];

?>