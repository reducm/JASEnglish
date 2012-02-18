<?php
		/**
		 * 
		 * Tom的工具类...
		 * @author TOM
		 *
		 */
		class Tool{
					public static function p ($mixed){
							echo "<pre>";
								if (is_array($mixed)){
											print_r($mixed);
										}else {
											echo "$mixed";
											}
							echo "</pre>";
					}					
		}
		
		
		/**
		 * JAS的工具类
		 * @author JAS
		 */		
		class JTool{
			/**
			 * 
			 * 方便打印数组
			 * @param 传入数组
			 * @return 无返回值 直接打印用格式过后的print_r直接打印数组
			 */
			public  static function printArray($ar) {
				echo "<pre>";
					print_r($ar);
				echo "</pre>";
			}
			
			/**
			 * 
			 * 打印字符串并换行
			 * @param 字符串
			 */
			public static function println($str) {
				echo $str."<br/>";
			}
			
			/**
			 * 
			 * 包装td
			 * @param unknown_type $str
			 */
			public static function td($str) {
				return "<td>".$str."</td>";
			}
			
			/**
			 * 
			 * 包装tr
			 * @param unknown_type $str
			 */
			public static function tr($str) {
				return "<tr>".$str."</tr>";
			}
			
			/**
			 * 
			 * 包装img标签链接
			 * @param unknown_type $src
			 * @param unknown_type $alt
			 */
			public static function img($src,$alt=null) {
				return "<img src=\"{$src}\" alt=\"{$alt}\" />";
			}
			
			public static function userTable(array $users){
				$str="<table class='followers'>";
					$trs="";
					foreach ($users as $key=>$each) {
						$temp =self::td($key).self::td($each['name']).self::td(self::img($each['profile_image_url'])).self::td($each['status']['text']);
						$trs.= self::tr($temp);
					}
				$str.=$trs."</table>";				
				return $str;
			}
			
			public static function buildWeiboLink($uid,$sid){
				return "http://api.t.sina.com.cn/{$uid}/statuses/{$sid}";
			}
		}
		
		

