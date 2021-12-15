import React, { Component } from 'react'
import { Link, Redirect,withRouter } from 'react-router-dom'
import { ValidatorForm } from 'react-form-validator-core'
import axios from 'axios'

import Swal from 'sweetalert2'
import InputValidator from './InputValidator';
import Loader from "react-loader-spinner";
import { MainContext } from './Context'
import Cookies from 'universal-cookie'
class Login extends Component {
    constructor(props) {
        super(props)
    
        this.state = {
            email:'',
            password:'',
            disableBtn: false,

            redirect:null,
        }

        this._onInputChange = this._onInputChange.bind(this);
    }

    
    _onInputChange = (e)=>{
        this.setState({[e.target.name]:e.target.value});
    }

    clearInput = ()=>{
        this.setState({email:'',password:''})
    }
    _onSubmit = async() => {



            //if form field valid

            this.setState({disableBtn:true});
            if(navigator.onLine){
                try{
                    const params={
                            email: this.state.email,
                            password: this.state.password
                    }
                    
                    const response = await axios.post('/Login',params);
                    
                    if(response.status === 200){
                        if(response.data.status === true){
                            this.clearInput();

                            //change the login state
                            this.context.updateState(
                                'auth',{isLoggedIn:true,token:response.data.token}
                            );
                            const cookies = new Cookies();
                            cookies.set('token', response.data.token, { path: '/', expires: new Date(Date.now()+(1000*86400*30))}); //30days
                            
                            Swal.fire({
                                title: "Success!",
                                text:"Login Successfully!",
                                icon:"success",
                                confirmButtonText: 'Go to Dashboard',
                                allowOutsideClick:false,
                            })
                            .then((result)=>{
                                if(result.isConfirmed){
                                    this.props.history.push("/dashboard");
                                }
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
                }catch(error){
                    //console.log(error.message);
                    Swal.fire("Oops!", "Network Error!", "error");
                }
            }else{
                //if network is not connected
                Swal.fire("No Network!", "Please check your network connection", "error");
            }
            
        //if failed to login then enable the login button
        if(!this.context.state.auth.isLoggedIn)
            this.setState({disableBtn:false});
    }
    render() {

        if(this.context.state.auth.isLoggedIn){
            return <Redirect to="/dashboard"/>;
        }
        const {email,password,disableBtn} = this.state;
        return (
            <div id="login" className="position-relative">
                <div className="login-container position-absolute bg-white shadow-md">
                <ValidatorForm             
                    onSubmit={this._onSubmit}
                >   
                    
                    <InputValidator
                        label="Email"
                        iconClass="bi bi-envelope"
                        onChange={this._onInputChange}
                        name="email"
                        className="form-control"
                        value={email}
                        placeholder="Enter your email"
                        validators={['required', 'isEmail']}
                        errorMessages={['This field is required', 'email is not valid']}
                    />

                    <InputValidator
                        label="Password"
                        iconClass="bi bi-lock"
                        onChange={this._onInputChange}
                        name="password"
                        className="form-control"
                        value={password}
                        placeholder="Enter your password"
                        validators={['required']}
                        errorMessages={['this field is required']}
                    />
                        


                    <div className="text-center">
                        <button className={disableBtn?("btn btn-primary disabled"):("btn btn-primary")}>
                        {disableBtn?
                            <Loader type="Bars" color="#FFF" height={20} width={35} />
                        :
                            'Login'
                        }
                        </button>
                    </div>
                    <div className="mt-3 text-center">
                        <p>Don't have an account? <Link to="/signup" className="text-decoration-none">Create</Link></p>
                    </div>
                </ValidatorForm>
                </div>
                
            </div>
        )
    }
}

Login.contextType = MainContext;

export default withRouter(Login);