var total = sexman + sexgirl; //总数 
var sizebig = 1.1;//鼠标移入后放大的倍数
// 创建SVG画布  
var svg = d3.select("#pie-chart"), 
widths = +svg.attr("width"),  
heights = +svg.attr("height"), 
    width = +svg.attr("width") / sizebig,  
    height = +svg.attr("height") / sizebig,  
    radius = Math.min(width, height) / 2;  
// 创建饼图布局  
var pie = d3.pie()  
    .value(function(d) { return d; })  
    .sort(null); // 不对扇区进行排序  
// 创建弧生成器  
var arc = d3.arc()  
    .innerRadius(0)  
    .outerRadius(radius);  
// 创建颜色比例尺  
var color = d3.scaleOrdinal()  
    .range(["var(--hover-color)", "var(--home-hover-color)"]);  
// 创建数据数组  
var data = [sexman, sexgirl];  
// 创建g元素用于容纳所有的图形元素  
var g = svg.append("g")  
    .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");  
// 绘制饼图的扇区  
var arcs = g.selectAll("path")  
    .data(pie(data))  
    .enter().append("path")  
    .attr("d", arc)  
    .attr("fill", function(d, i) { return color(i); });  
// 添加文本标签  
g.selectAll("text")  
    .data(pie(data))  
    .enter().append("text")  
    .attr("dy", ".35em") // 垂直位置调整  
    .attr("text-anchor", "middle") // 文本水平居中  
    .attr("transform", function(d) {  
        // 将文本标签放置在扇区的中心，并且不旋转  
        var centroid = arc.centroid(d);  
        return "translate(" + centroid + ")";  
    })  
    .text(function(d, i) {  
        // 根据扇区的存在与否显示对应的标签  
        if (d.data > 0) {  
            return i === 0 ? "男" : "女";  
        } else {  
            return ""; // 如果数据为0，则不显示文本  
        }
    });
    //鼠标移动到扇形区域上时，放大该扇形区域并显示百分比值
    arcs.on("mouseover", function(event,d) {
        var percents = (d.data / total * 100).toFixed(2);
        //去除percent小数
        if(percents == 0){
           var percent = "0%";
        }else if(percents == 100){
           var percent = "100%";
        }else{
           var percent = percents + "%";
        }

        d3.select(this) 
            .transition()  
            .duration(200)  
            .attr("d", arc.innerRadius(0).outerRadius(radius * sizebig));
            d3.select(this.parentNode) 
            .append("text")
            .attr("class", "percent") 
            .attr("transform", function() {
                var centroid = arc.centroid(d);
                centroid[0] *= sizebig; 
            })
            .text(percent);
    });
    //鼠标移出后恢复原状
    arcs.on("mouseout", function(d) {
        d3.select(this)  
            .transition()  
            .duration(200)  
            .attr("d", arc.innerRadius(0).outerRadius(radius));
            d3.select(this.parentNode)  
            .select(".percent")  
            .remove();
    });
    //g元素移动到画布中心
        g.attr("transform", "translate(" + widths / 2 + "," + heights/ 2 + ")");