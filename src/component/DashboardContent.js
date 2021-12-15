import React from 'react'
import { ValidatorForm } from 'react-form-validator-core'
import Loader from "react-loader-spinner";
import TextComponent from './TextComponent';
import ItemCount from './ItemCount';

import MonitorCard from './MonitorCard';
import { withRouter } from 'react-router-dom';
import Swal from 'sweetalert2'
import InputValidator from './InputValidator';
import axios from 'axios'
import Cookies from 'universal-cookie'
import ReactPaginate from 'react-paginate';
import { MainContext } from './Context';


const interval = [1,2,3,4,5,10,30,60];
class DashboardContent extends React.Component {
    constructor(props) {
        super(props)
    
        this.state = {
            modalDisplay:false,
            disableBtn:false,
            title:'',
            url:'',
            interval:1,
            monitorsData:[],
            totalMonitor:0,
            upMonitor:0,
            downMonitor:0,
            currentTime:'',

            //pagination
            pageCount:1,
            perPage:6,
            pageNo:0
        }
    }

    componentDidMount(){
        
        this.interval = setInterval(() => this.setState({ currentTime: new Date().getTime() }), 1000);
        this.updateInterval = setInterval(() => this.fetchMonitorsData(), 10000);
        this.fetchMonitorsData();
  
    }

    componentWillUnmount() {

    clearInterval(this.interval);
    clearInterval(this.updateInterval);
    }

    fetchMonitorsData = async() =>{
        //console.log(this.context.state.auth.token);
        if(navigator.onLine){
            try{
                const response = await axios.post('/Monitor/fetch_all_monitors/?pageno='+this.state.pageNo,{
                    pageno:this.state.pageNo
                },{
                    headers:{
                        "Authorization" : `Bearer ${this.context.state.auth.token}`
                    }
                });
                
                if(response.status === 200){
                    this.setState({
                        monitorsData:response.data.monitors_data,
                        totalMonitor:response.data.total_monitors,
                        upMonitor:response.data.up_monitors,
                        downMonitor:response.data.down_monitors,
                        pageCount:Math.ceil(response.data.total_monitors/this.state.perPage)
                    });

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
    }
    openModal = ()=>{
        this.setState({modalDisplay:true});
    }

    closeModal = ()=>{
        this.setState({modalDisplay:false});
    }

    clearInput = ()=>{
        this.setState({
            title:'',
            url:'',
            interval:1
        });
    }

    _onChangePage = (data) =>{
        let selected =  data.selected;
        this.setState({pageNo:selected},()=>{
            this.fetchMonitorsData();
        });
    }
    _onChangeInput = (e)=>{
        this.setState({
            [e.target.name]: e.target.value
        });
    }
    _onChangeInterval = (e)=>{
        if(interval.indexOf(e.target.value-1) !== -1){
            this.setState({
                interval:interval[e.target.value-1]
            });
        }
    }

    _onSubmit = async() =>{

        this.setState({disableBtn:true});
        if(navigator.onLine){
            try{
                const params={
                        title: this.state.title,
                        url: this.state.url,
                        interval: this.state.interval
                }
                    const cookies = new Cookies();
                    let token;
                    token = cookies.get('token')
                    if(token){
                        const response = await axios.post('/Monitor/add',params,
                            {
                                headers:{
                                    "Authorization" : `Bearer ${token}`
                                }
                            }
                        );
                        
                        if(response.status === 200){
                            if(response.data.status === true){
                                this.clearInput();
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
        const {totalMonitor,upMonitor,downMonitor} = this.state;
        return (
            <>
                <div id="content" className="py-4 px-5">
                    <TextComponent heading="Welcome to Dashboard" text="Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs."/>
                    <div className="row item-count-container justify-content-between my-3">
                        <ItemCount 
                            title="Total Monitor"
                            count={totalMonitor}
                            className="bg-primary-20"
                            headerLabelClass="text-black"
                            countClass="text-primary"
                            columns ="col-sm-6 col-md-6 col-lg-4 col-6"
                            />
                        <ItemCount 
                            title="Up Monitor"
                            count={upMonitor}
                            className="bg-success-20"
                            headerLabelClass="text-black"
                            countClass="text-success"
                            columns ="col-sm-6 col-md-6 col-lg-4 col-6"
                            />
                        <ItemCount 
                            title="Down Monitor"
                            count={downMonitor}
                            className="bg-danger-20"
                            headerLabelClass="text-black"
                            countClass="text-danger"
                            columns ="col-sm-6 col-md-6 col-lg-4 col-6"
                            />
                        {/*
                        <ItemCount 
                            title="Paused Monitor"
                            count="35"
                            className="bg-cyan-20"
                            headerLabelClass="text-black"
                            countClass="text-cyan"
                            />
                        */}
                    </div>

                    <div className="row justify-content-between mb-3 mt-5">
                        <div className="col-md-6">
                            <h2>Monitors</h2>
                        </div>
                        <div className="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <button className="btn btn-primary" onClick={this.openModal}><i className="bi bi-plus"/> Add Website</button>
                        </div>
                    </div>

                    <div className="row">
                        {
                            Array.isArray(this.state.monitorsData)?
                                this.state.monitorsData.map((monitor,index)=>{
                                    //console.log(this.props.match.path);
                                    let link =this.props.match.path+`/${monitor.id}`;
                                    let last_checked =  parseInt((this.state.currentTime - Date.parse(monitor.last_checked)) /1000);
                                    //console.log(last_checked);
                                    return <MonitorCard
                                                key = {index}
                                                url = {link}
                                                name = {monitor.title} 
                                                status = {monitor.status}
                                                performance = {monitor.response_time}
                                                uptime = {monitor.uptime}
                                                lastChecked = {last_checked}
                                                lastIncident = {monitor.last_incident}
                                                />
                            })
                            : ''
                        }
                    </div>
                    <div className="row">
                        <ReactPaginate
                            previousLabel={<i className="bi bi-caret-left"/>}
                            nextLabel={<i className="bi bi-caret-right"/>}
                            breakLabel={'...'}
                            breakClassName={'break-me'}
                            pageCount={this.state.pageCount}
                            marginPagesDisplayed={2}
                            pageRangeDisplayed={5}
                            onPageChange={this._onChangePage}
                            containerClassName={'pagination justify-content-center'}
                            activeClassName={'active-page'}
                            activeLinkClassName={'active-page-a'}
                            />
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
                                    onSubmit={this._onSubmit}
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
                                    <InputValidator
                                        label="Monitor URL"
                                        iconClass="bi bi-envelope"
                                        onChange={this._onChangeInput}
                                        name="url"
                                        className="form-control"
                                        value={this.state.url}
                                        placeholder="URL (eg. https://example.com)"
                                        validators={['required', 'matchRegexp:((http|https)://)(www.)?[a-zA-Z0-9@:%._\\+~#?&//=]{2,256}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%._\\+~#?&//=]*)']}
                                        errorMessages={['This field is required', 'Please enter valid URL']}
                                    />

                                    <label htmlFor="interval" className="form-label">Interval</label>
                                    <div className="input-group mb-3">
                                        <div className="input-group-prepend">
                                            <label className="input-group-text" htmlFor="interval">
                                                <i className="bi bi-bell"/>
                                            </label>
                                        </div>
                                        <select className="custom-select" id="interval" defaultValue="1" name="interval" onChange={this._onChangeInterval}>
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

DashboardContent.contextType = MainContext;
export default withRouter(DashboardContent);
