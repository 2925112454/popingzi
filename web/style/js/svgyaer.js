function getMaxValueFromObject(obj) {  
    let maxValue = -Infinity; // 初始化最大值为负无穷大  
    for (let key in obj) {  
        if (obj.hasOwnProperty(key)) { // 确保key是对象的直接属性  
            let value = obj[key];  
            if (value > maxValue) { // 如果当前值大于已知的最大值  
                maxValue = value; // 更新最大值  
            }  
        }  
    }  
    return maxValue;  
} 
var maxYearValue = getMaxValueFromObject(toyear);//获取数据中最大的值
var maxYearValueLength = maxYearValue.toString().length;//获取最大值的位数
if (maxYearValueLength<=1){
    var maxYearValueLengthx=maxYearValueLength+2.1;
}else if (maxYearValueLength==2){
    var maxYearValueLengthx=maxYearValueLength+1;
}else if (maxYearValueLength==3){
    var maxYearValueLengthx=maxYearValueLength+0.5;
}else if (maxYearValueLength==4){
    var maxYearValueLengthx=maxYearValueLength+0.5;
}else if (maxYearValueLength>4){
    var maxYearValueLengthx=maxYearValueLength;
}else{
    var maxYearValueLengthx=maxYearValueLength;
}
var widthx = 580;//画布宽度
var heighty = 180;//画布高度
var margin = {top: 30, right:20, bottom: 20, left:(maxYearValueLengthx)*9,},  
    width = widthx - margin.left - margin.right,  
    height = heighty - margin.top - margin.bottom;  
  
var x = d3.scaleBand()  
    .rangeRound([0, width])  
    .padding(0.1)  
    .align(0.1);  
  
var y = d3.scaleLinear()  
    .rangeRound([height, 0]);  
  
var svg = d3.select("#chart")  
    .append("svg")  
    .attr("width", width + margin.left + margin.right)  
    .attr("height", height + margin.top + margin.bottom)  
    .append("g")  
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");  
  
var years = Object.keys(toyear).sort(d3.ascending);  
var values = years.map(function(year) { return toyear[year]; });  
  
x.domain(years);  
y.domain([0, d3.max(values)]);  
  
svg.append("g")  
    .attr("class", "axis axis--x")  
    .attr("transform", "translate(0," + height + ")")  
    .call(d3.axisBottom(x));  
  
svg.append("g")  
    .attr("class", "axis axis--y") 
    .attr("transform", "translate(-5,0)")   
    .call(d3.axisLeft(y));

var bars = svg.selectAll(".bar")  
    .data(years)  
    .enter().append("rect")  
    .attr("class", "bar")  
    .attr("x", function(d) { return x(d)+5; })  
    .attr("y", function(d) { return y(toyear[d]) -5; })  
    .attr("height", function(d) { return height - y(toyear[d]); })  
    .attr("width", x.bandwidth());  
  
// 添加提示元素  
var tooltip = svg.append("text")  
    .attr("class", "tooltip")  
    .style("opacity", 0)  
    .attr("text-anchor", "middle") // 水平居中  
    .attr("dy", "-0.1em"); // 垂直调整，确保文本在条形图上方显示
    bars.on("mouseover", function(event, d) {  
        // 显示提示并设置其位置和文本  
        var xPos = parseFloat(d3.select(this).attr("x")) + x.bandwidth() / 2;  
        var yPos = parseFloat(d3.select(this).attr("y")) - 10;  
        tooltip  
            .text(toyear[d]) // 使用数据对象d来访问年份和对应的值  
            .style("opacity", 1)  
            .attr("x", xPos)  
            .attr("y", yPos);  
    })  
    .on("mouseout", function() {  
        // 隐藏提示  
        tooltip.style("opacity", 0);  
    });