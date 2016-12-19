var all_site = [
    {county: '基隆市', sitename: ['基隆']},
    {county: '嘉義市', sitename: ['嘉義']},
    {county: '高雄市', sitename: ['美濃','大寮','橋頭','仁武','鳳山','林園','楠梓','左營','前金','前鎮','小港','復興']},
    {county: '新北市', sitename: ['汐止','萬里','新店','土城','板橋','新莊','菜寮','林口','淡水','三重','永和']},
    {county: '臺北市', sitename: ['士林','中山','萬華','古亭','松山','大同','陽明']},
    {county: '桃園市', sitename: ['桃園','大園','觀音','平鎮','龍潭','中壢']},
    {county: '新竹縣', sitename: ['湖口','竹東']},
    {county: '新竹市', sitename: ['新竹']},
    {county: '苗栗縣', sitename: ['頭份','苗栗','三義']},
    {county: '臺中市', sitename: ['豐原','沙鹿','大里','忠明','西屯']},
    {county: '彰化縣', sitename: ['彰化','線西','二林']},
    {county: '南投縣', sitename: ['南投','竹山','埔里']},
    {county: '雲林縣', sitename: ['斗六','崙背','臺西','麥寮']},
    {county: '嘉義縣', sitename: ['新港','朴子']},
    {county: '臺南市', sitename: ['新營','善化','安南','臺南']},
    {county: '屏東縣', sitename: ['屏東','潮州','恆春']},
    {county: '臺東縣', sitename: ['臺東','關山']},
    {county: '宜蘭縣', sitename: ['宜蘭','冬山']},
    {county: '花蓮縣', sitename: ['花蓮']},
    {county: '澎湖縣', sitename: ['馬公']},
    {county: '連江縣', sitename: ['馬祖']},
    {county: '金門縣', sitename: ['金門']}
]

// loading 
function loading(isloading = true) {
    $('#loading').css('display', isloading ? 'block' : 'none')
}

// search county then return index
function searchSiteIndex(county) {
    for(var i = 0, length1 = all_site.length; i < length1; i++){
        if (all_site[i].county == county) {
            return i
        }
    }
}

// change sitename when county selected
function loadSite() {
    var index = searchSiteIndex($('#county').val())
    $('#sitename').empty() // 清空
    // 加入新的<option>
    for(var i = 0, length1 = all_site[index].sitename.length; i < length1; i++){
        var option = $('<option></option>').attr('value', all_site[index].sitename[i]).text(all_site[index].sitename[i])
        $('#sitename').append(option)
    }
}

// global var
var year, two_month, sitename, pollution;

// reset checkbox
function reset(re) {
    if (re == "date") {
        $('.input-year:checkbox').map(function() {  
            if ($(this).prop('checked')) $(this).prop('checked', false);  
        }).get()
        $('.input-month:radio').map(function() {  
            if ($(this).prop('checked')) $(this).prop('checked', false);  
        }).get()
     } else {
        $('.input-pollution:radio').map(function() {  
            if ($(this).prop('checked')) $(this).prop('checked', false);  
        }).get()
     }
}

// set search data
function setData() {
    // get value that selected checkbox
    year = $('.input-year:checkbox').map(function() {  
        if ($(this).prop('checked')) return $(this).val();  
    }).get()
    // get value that selected radiobox
    two_month = $('.input-month:radio').map(function() {  
        if ($(this).prop('checked')) return $(this).val();  
    }).get()
    // get value that selected checkbox
    pollution = $('.input-pollution:radio').map(function() {  
        if ($(this).prop('checked')) return $(this).val();  
    }).get()
    // get sitename
    sitename = $('#sitename').val()
    showData()
}

// show user set data on table
function showData() {
    $('#show-year').html(year.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
    year.forEach(function (element, index, array) {
        $('#show-year').append(element+"年,")
    })
    $('#show-month').html(two_month.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
    two_month.forEach(function (element, index, array) {
        $('#show-month').append(element+"月~"+(parseInt(element)+1)+"月")
    })
    $('#show-site').html(sitename+"站")
    $('#show-pollution').html(pollution.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
    pollution.forEach(function (element, index, array) {
        $('#show-pollution').append(element.toUpperCase())
    })
}

