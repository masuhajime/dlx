<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>dlx</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>
    $(function(){
        $('#get_hair').click(function(){
            var id_from = $('#hair_id_from').val();
            var id_to = $('#hair_id_to').val();
            var html = '';
            for (; id_from<=id_to; id_from++) {
                html += '<span><img src="http://dragonx.asobism.co.jp/top/img/avatar/m'+id_from+'.gif"><br>'+id_from +'</span>';
                if (id_from % 5 == 0) {
                    html += "<br>\n";
                }
            }
            $('#hairs').html(html);
        });
    })
</script>
</head>
<body>
    <h3>画像の取得</h3>
    <p>
    id:from<input type="text" id="hair_id_from" value="3100115"><br>
    id:to<input type="text" id="hair_id_to" value="3100115"><br>
    <input type="submit" value="get" id="get_hair">
    </p>
    <h3>画像(髪型)</h3>
    <p id="hairs"></p>
</body>
</html>
