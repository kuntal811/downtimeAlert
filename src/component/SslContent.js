import React, { Component } from 'react'
import SslMonitorCard from './SslMonitorCard'
import { ValidatorForm } from 'react-form-validator-core'
import Loader from "react-loader-spinner";
import Swal from 'sweetalert2'
import InputValidator from './InputValidator';
import axios from 'axios'
import Cookies from 'universal-cookie'
import ReactPaginate from 'react-paginate';
import { MainContext } from './Context';


export default class SslContent extends Component {
    constructor(props) {
        super(props)
    
        this.state = {
            modalDisplay:false,
            disableBtn:false,
            title:'',
            url:'',
            remind_before_days:30,
            monitors:[],

            //pagination
            pageCount:1,
            perPage:6,
            pageNo:0
        }
    }

    componentDidMount(){
        this.fetchMonitorsData();
        this.updateInterval = setInterval(() => this.fetchMonitorsData(), 10000);
    }

    componentWillUnmount() {

    clearInterval(this.updateInterval);
    }

    fetchMonitorsData = async() =>{
        /*console.log(this.context.state.auth.token);*/
        if(navigator.onLine){
            try{
                const response = await axios.post('/Ssl/fetch_all_ssl_monitors?pageno='+this.state.pageNo,{

                },{
                    headers:{
                        "Authorization" : `Bearer ${this.context.state.auth.token}`
                    }
                });
                
                if(response.status === 200){
                    this.setState({
                        monitors:response.data.ssl_monitors,
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
    _onSubmit = async() =>{

        this.setState({disableBtn:true});
        if(navigator.onLine){
            try{
                const params={
                    url: this.state.url,
                    remind_before_days:this.state.remind_before_days
                }
                const cookies = new Cookies();
                let token  = cookies.get('token')
                if(token){
                    const response = await axios.post('/Ssl/add',params,
                        {
                            headers:{
                                "Authorization" : `Bearer ${token}`
                            }
                        }
                    );
                    
                    if(response.status === 200){
                        if(response.data.status === true){
                            this.setState({url:''});
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

    _onDelete = async(id)=>{
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
                        const response = await axios.post('/Ssl/delete_ssl_monitor',{
                            ssl_id:id
                        },{
                            headers:{
                                "Authorization" : `Bearer ${this.context.state.auth.token}`
                            }
                        });
                        
                        if(response.status === 200){
                            if(response.data.status){
                                Swal.fire("Deleted!", response.data.message, "success");
                                this.fetchMonitorsData();
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


    render() {
        const {monitors} = this.state;
        return (
            <>
                <div id="content" className="py-4 px-5">

        
                    <div className="row justify-content-between mb-3 mt-5">
                        <div className="col-md-6">
                            <h2>SSL Monitors</h2>
                        </div>
                        <div className="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <button className="btn btn-primary" onClick={this.openModal}><i className="bi bi-plus icon-md"/> Add Website</button>
                        </div>
                    </div>
        
                    <div className="row">
                        {
                            monitors.map((monitor,index)=>{
                                
                                let start_t = null;
                                let exp_t = null;
                                if(monitor.start_date){
                                    let t1 = new Date(monitor.start_date)
                                    start_t = t1.getDate()+'-'+t1.getMonth()+'-'+t1.getFullYear();
                                }
                                if(monitor.end_date){
                                    let t2 = new Date(monitor.end_date)
                                    exp_t = t2.getDate()+'-'+t2.getMonth()+'-'+t2.getFullYear();
                                }
                                
                                return <SslMonitorCard
                                        id ={monitor.ssl_id}
                                        key = {index}
                                        protocol={monitor.protocol}
                                        name = {monitor.url} 
                                        organization = {monitor.issuer}
                                        issue_date = {start_t}
                                        expiry = {exp_t}
                                        algorithm = {monitor.algorithm}
                                        remind_before_days = {monitor.remind_before_days}
                                        alert_status = {monitor.alert_status}
                                        fetched = {monitor.end_date?true:false}
                                        onDelete={this._onDelete}
                                        />
                            })
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
                                    <h5 className="modal-title">SSL Details</h5>
                                    <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close" onClick={this.closeModal}></button>
                                </div>
                                <div className="modal-body">
                                    <ValidatorForm 
                                        onSubmit={this._onSubmit}
                                    > 
                                    {/*
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
                                        */}
                                        <InputValidator
                                            label="SSL URL"
                                            iconClass="bi bi-envelope"
                                            onChange={this._onChangeInput}
                                            name="url"
                                            className="form-control"
                                            value={this.state.url}
                                            placeholder="URL (eg. https://example.com)"
                                            validators={['required', 'matchRegexp:((http|https)://)(www.)?[a-zA-Z0-9@:%._\\+~#?&//=]{2,256}\\.[a-z]{2,6}\\b([-a-zA-Z0-9@:%._\\+~#?&//=]*)']}
                                            errorMessages={['This field is required', 'Please enter valid URL']}
                                        />
                                        <InputValidator
                                            label="Remind time (days)"
                                            iconClass="bi bi-envelope"
                                            onChange={this._onChangeInput}
                                            name="remind_before_days"
                                            className="form-control"
                                            value={this.state.remind_before_days}
                                            placeholder="Enter number of days you want to be reminded"
                                            validators={['required', 'isNumber']}
                                            errorMessages={['This field is required', 'Please enter valid number']}
                                        />

                                        {/*
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
                                        */}
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

SslContent.contextType = MainContext;