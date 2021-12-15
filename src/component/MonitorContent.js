import React from 'react'
import axios from 'axios';
import Swal from 'sweetalert2';
import { ValidatorForm } from 'react-form-validator-core'
import ItemCount from './ItemCount';
import InputValidator from './InputValidator';
import Loader from 'react-loader-spinner';
import Cookies from 'universal-cookie';

import { withRouter } from 'react-router-dom';
import AreaChart from './AreaChart';

import { MainContext } from './Context';



const interval_arr = [1,2,3,4,5,10,30,60];
class MonitorContent extends React.Component {
    constructor(props) {
        super(props)
    
        this.state = {
            modalDisplay:false,
            disableBtn:false,
            title:'',
            url:'',
            interval:'',
            monitorsData:{
                id:'',
                title:'',
                uptime:'',
                url:'',
                status:'',
                created_at:'',
                avg_response_time:'',
                interval:'',
                is_active:'',
                last_checked:'',
                protocol:'',
                monitor_checks:[],
                monitor_graph:[],
            },
        }
    }

    componentDidMount(){
        this.fetchMonitorsData();
        //this.interval = setInterval(() => this.setState({ currentTime: new Date().getTime() }), 1000);
        this.updateInterval = setInterval(() => this.fetchMonitorsData(), 30000);
    }

    componentWillUnmount() {

    //clearInterval(this.interval);
    clearInterval(this.updateInterval);
    }

    openModal = ()=>{
        this.setState({modalDisplay:true});
    }

    closeModal = ()=>{
        this.setState({modalDisplay:false});
    }

    
    _onChangeInput = (e)=>{
        this.setState({
            [e.target.name]: e.target.value
        });
    }
    _onChangeInterval = (e)=>{
        if(interval_arr[e.target.value - 1]){
            this.setState({
                interval:interval_arr[e.target.value-1] * 60,
            });
        }
    }

    fetchMonitorsData = async() =>{
        //console.log(this.props.match.params.monitorId);
        if(navigator.onLine){
            try{
                const response = await axios.post('/Monitor/monitor_details',{
                    monitor_id:this.props.match.params.monitorId
                },{
                    headers:{
                        "Authorization" : `Bearer ${this.context.state.auth.token}`
                    }
                });
                
                if(response.status === 200){
                    this.setState({monitorsData:response.data});

                }else{
                    //request status is not  200
                    Swal.fire("Oops!", "Something went wrong!!", "error");
                }
            }catch(error){
                //console.log(error.message);
                Swal.fire("Oops!", "Network Error!", "error");
            }
        }else{
            //if network is not connected
            Swal.fire("No Network!", "Please check your network connection", "error");
        }

        // update edit form input value for first time only
        if(this.state.title === '')
            this.setState({
                title:this.state.monitorsData.title,
                url:(this.state.monitorsData.protocol+'://'+this.state.monitorsData.url),
                interval:this.state.monitorsData.interval
            });
    }


    _onDelete = async()=>{
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#A855F7',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
          }).then(async(result) => {
            if (result.isConfirmed) {
                if(navigator.onLine){
                    try{
                        const response = await axios.post('/Monitor/delete_monitor',{
                            monitor_id:this.props.match.params.monitorId
                        },{
                            headers:{
                                "Authorization" : `Bearer ${this.context.state.auth.token}`
                            }
                        });
                        
                        if(response.status === 200){
                            if(response.data.status){
                                Swal.fire("Deleted!", response.data.message, "success");
                                this.props.history.push("/dashboard");
                            }else{
                                Swal.fire("Failed!", response.data.message, "error");
                            }
        
                        }else{
                            //request status is not  200
                            Swal.fire("Oops!", "Request did not succeed!!", "error");
                        }
                    }catch(error){
                        //console.log(error.message);
                        Swal.fire("Oops!", "Network Error!", "error");
                    }
                }else{
                    //if network is not connected
                    Swal.fire("No Network!", "Please check your network connection", "error");
                }
            }
          })
    }
    


    _onUpdate = async()=>{

        this.setState({disableBtn:true});
        if(navigator.onLine){
            try{
                const params={
                        title       : this.state.title,
                        interval    : this.state.interval,
                        monitor_id  :this.props.match.params.monitorId,
                }
                    const cookies = new Cookies();
                    let token;
                    token = cookies.get('token')
                    if(token){
                        const response = await axios.post('/Monitor/update',params,
                            {
                                headers:{
                                    "Authorization" : `Bearer ${token}`
                                }
                            }
                        );
                        
                        if(response.status === 200){
                            if(response.data.status === true){
                                this.closeModal();
                                Swal.fire({
                                    title: "Success!",
                                    text: response.data.message,
                                    icon:"success",
                                })

                                //window.location.href="/dashboard"

                            }
                            else{
                                Swal.fire("Oops!", response.data.message, "error");
                            }


                        }else{
                            //request status is not  200
                            Swal.fire("Oops!", "Something went wrong!!", "error");
                        }
                    }else{
                        Swal.fire("Oops!", "Something went wrong!!", "error");
                    }
                }catch(error){
                    //console.log(error.message);
                    Swal.fire("Oops!", "Network Error!", "error");
                }
        }else{
            //if network is not connected
            Swal.fire("No Network!", "Please check your network connection", "error");
        }
        this.setState({disableBtn:false});
    }
    

    render(){
        const {title,uptime,url,status,created_at,avg_response_time,interval,is_active,incident_count,protocol,monitor_checks,monitor_graph} = this.state.monitorsData;
        
        let datas = [];
        if(monitor_graph){
            monitor_graph.map((data,index)=>{
                let insert_data = {x:new Date(data.checked_at),y:parseInt(data.response_time)};
                datas.push(insert_data);
                return 1;
            });
        }

        
        return (
            <>
                <div id="content" className="py-4 px-5">
                    <div className="row justify-content-between">
                        <div className="col-md-4 col-lg-4">
                            <div className="status-card card">
                                <div className="card-body">
                                    <h3 className="title text-capitalize">
                                        {title}
                                        <span className={"current-status "+(status===null?'':(status !==0?'online':'offline'))}>{status === null?'':(status !==0 ?'online':'offline')}</span>
                                    </h3>
                                    <small className="website">{protocol+'://'+url}</small>
                                </div>
                            </div>
                        </div>
                        <div className="col-6 col-md-4 col-lg-3 my-3 my-md-0">
                            <div className="monitor-action bg-white d-flex justify-content-between p-1">
                                <button className="bg-succes">
                                    <i className="bi bi-bell"/>
                                    Alert
                                </button>
                                <button className="bg-succes" onClick={this.openModal}>
                                    <i className="bi bi-pen"/>
                                    Edit
                                </button>
                                <button className="text-danger" onClick={this._onDelete}>
                                    <i className="bi bi-trash"/>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div className="row item-count-container justify-content-between my-3">
                            <div className="col-12 col-md-6 col-lg-8 order-2 order-md-1">
                                <div className="row">
                                    <ItemCount
                                        key={3856377777777775}
                                        title="Avg. response"
                                        count={`${monitor_checks.length===0?'---':(avg_response_time+'ms')}`}
                                        className="bg-primary-20"
                                        headerLabelClass="text-black"
                                        countClass="text-primary"
                                        columns ="col-sm-6 col-md-6 col-lg-3 col-6"
                                        />
                                    <ItemCount 
                                        key = {385637584655}
                                        title="Up time"
                                        count={`${monitor_checks.length===0?'---':(parseFloat(uptime).toFixed(2)+'%')}`}
                                        className="bg-success-20"
                                        headerLabelClass="text-black"
                                        countClass="text-success"
                                        columns ="col-sm-6 col-md-6 col-lg-3 col-6"
                                        />
                                    <ItemCount 
                                        key = {385636589684655}
                                        title="Down time"
                                        count={`${monitor_checks.length===0?'---':(parseFloat(100-uptime).toFixed(2)+'%')}`}
                                        className="bg-danger-20"
                                        headerLabelClass="text-black"
                                        countClass="text-danger"
                                        columns ="col-sm-6 col-md-6 col-lg-3 col-6"
                                        />
                                    <ItemCount 
                                        key = {385635593767655}
                                        title="Incident"
                                        count={incident_count}
                                        className="bg-cyan-20"
                                        headerLabelClass="text-black"
                                        countClass="text-cyan"
                                        columns ="col-sm-6 col-md-6 col-lg-3 col-6"
                                        />
                            </div>


                        {/*                 Checks                  */}
                            <div className="col-md-12 mt-4">
                                <div className="checked-table table-responsive p-1">
                                    <table className="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Time</th>
                                            <th scope="col">Code</th>
                                            <th scope="col">Response</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {monitor_checks.length===0?<tr><td colSpan="4" className="text-center border-none">No data to show. Wait till first check</td></tr>:''}
                                        {
                                            monitor_checks.map((check,index)=>{
                                                return(
                                                    <tr>
                                                        <td>{check.checked_at}</td>
                                                        <td>{check.response_code}</td>
                                                        <td>{check.response_time}</td>
                                                        <td>{check.status}</td>
                                                    </tr>
                                                )
                                            })
                                            }
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div>
                            <div className="col-md-12 mt-4 mb-4">
                                <AreaChart data={datas}/>
                            </div>
                        </div>
                        <div className="col-12 col-md-6 col-lg-4 order-1 order-md-2 mb-3 mb-md-0">
                            <div className="card monitor-card">
                                <div className="card-header">
                                    <h5 className="card-title">
                                        Monitor Details
                                        <span className={"badge "+(is_active?'text-success':'text-warning')}>
                                            {is_active?'Running':'Paused'}
                                        </span>
                                    </h5>
                                </div>
                                <div className="card-body">
                                    <div className="row">
                                        <div className="col-6 col-md-6">
                                            <h5>Interval</h5>
                                            <p>Every {interval} sec</p>
                                        </div>
                                        <div className="col-6 col-md-6">
                                            <h5>Protocol</h5>
                                            <p className="text-uppercase">{protocol}</p>
                                        </div>
                                    </div>
                                    <div className="row">
                                        <div className="col-6 col-md-6">
                                            <h5>Tolerance</h5>
                                            <p>None</p>
                                        </div>
                                        <div className="col-6 col-md-6">
                                            <h5>Created</h5>
                                            <p>{created_at}</p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div className="container">
                        <div className="modal" style={this.state.modalDisplay?{display:'block'}:{display:'none'}}>
                            <div className="modal-dialog row">
                                <div className="modal-content">
                                <div className="modal-header">
                                    <h5 className="modal-title">Monitor Details</h5>
                                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close" onClick={this.closeModal}></button>
                                </div>
                                <div className="modal-body">
                                    <ValidatorForm 
                                        ref="form"
                                        onSubmit={this._onUpdate}
                                    > 
                                        <InputValidator
                                            label="Title"
                                            iconClass="bi bi-envelope"
                                            onChange={this._onChangeInput}
                                            name="title"
                                            className="form-control"
                                            value={this.state.title}
                                            placeholder="Monitor Title"
                                            validators={['required', 'matchRegexp:^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$']}
                                            errorMessages={['This field is required', 'Title should contain only text and white spaces']}
                                        />
                                        <div className="mb-3">
                                            <label htmlFor="url" className="form-label">Monitor URL</label>
                                            <div className="input-group">
                                                <span className="input-group-text"><i className="bi bi-envelope"/></span>
                                                <input className="form-control" name="url" readOnly value={this.state.url}
                                                />
                                            </div>
                                            <small className="text-muted">
                                                You can't update the URL
                                            </small>
                                        </div>

                                        <label htmlFor="interval" className="form-label">Interval</label>
                                        <div className="input-group mb-3">
                                            <div className="input-group-prepend">
                                                <label className="input-group-text" htmlFor="interval">
                                                    <i className="bi bi-bell"/>
                                                </label>
                                            </div>
                                            <select 
                                                className="custom-select"
                                                id="interval"
                                                value={
                                                        interval_arr.indexOf((this.state.interval)/60) !== -1
                                                        ?
                                                            interval_arr.indexOf((this.state.interval)/60)+1 // calculate the interval value option
                                                        :
                                                            interval_arr[1]
                                                    }
                                                name="interval"
                                                onChange={this._onChangeInterval}>
                                                    <option>Choose...</option>
                                                    <option value="1">Every 1 minute</option>
                                                    <option value="2">Every 2 minutes</option>
                                                    <option value="3">Every 3 minutes</option>
                                                    <option value="4">Every 4 minutes</option>
                                                    <option value="5">Every 5 minutes</option>
                                                    <option value="6">Every 10 minutes</option>
                                                    <option value="7">Every 30 minutes</option>
                                                    <option value="8">Every 60 minutes</option>
                                            </select>
                                        </div>
                                        <div className="modal-footer">
                                            <button type="button" className="btn btn-secondary" data-bs-dismiss="modal" onClick={this.closeModal}>Close</button>
                                            <button type="submit" className={this.state.disableBtn?("btn btn-primary disabled"):("btn btn-primary")}>
                                                {this.state.disableBtn?
                                                    <Loader type="Bars" color="#FFF" height={20} width={35} />
                                                :
                                                    'Add'
                                                }
                                            </button>
                                        </div>
                                    </ValidatorForm>

                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </>
            
        )
    }
}

MonitorContent.contextType = MainContext;
export default withRouter(MonitorContent);
