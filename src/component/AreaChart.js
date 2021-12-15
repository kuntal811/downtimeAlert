import React from 'react'
import CanvasJSReact from './../assets/canvasjs.react';
var CanvasJSChart = CanvasJSReact.CanvasJSChart;

export default class AreaChart extends React.Component{
  constructor(props) {
    super(props)
  
    this.state = {
        graphData:[]
    }
    this.toggleDataSeries = this.toggleDataSeries.bind(this);
  }
  componentDidMount(){
    this.correctData();
  }
  toggleDataSeries(e){
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else{
			e.dataSeries.visible = true;
		}
		this.chart.render();
	}
  correctData = ()=>{
    let datas = [];
    this.props.data.map((data,index)=>{
      let insert_data = {x:data.checked_at,y:data.response_time};
      datas.push(insert_data);
      return 1;
    });

    this.setState({
      graphData: datas
    });
  }
  render(){

    //console.log(this.props.data);
    const options = {
      theme: "light2",
      animationEnabled: true,
      exportEnabled: false,
      title: {
      text: ""
      },
      axisY: {
      title: "Response Time(ms)"
      },
      toolTip: {
      shared: true
      },
      legend: {
      verticalAlign: "center",
      horizontalAlign: "right",
      reversed: true,
      cursor: "pointer",
      itemclick: this.toggleDataSeries
      },
      data: [
      {
        type: "stackedArea",
        name: "Response time",
        showInLegend: false,
        xValueFormatString: "YYYY",
        dataPoints: this.props.data
      }
    ]
  }
    return (
      <div className="bg-white p-5 chart-container">
			<CanvasJSChart options = {options} 
				onRef={ref => this.chart = ref}
			/>
			{/*You can get reference to the chart instance as shown above using onRef. This allows you to access all chart properties and methods*/}
		</div>
    )
  }

}
