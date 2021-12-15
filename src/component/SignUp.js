import React, { Component } from 'react'
import { Link,Redirect, withRouter } from 'react-router-dom'
import { ValidatorForm } from 'react-form-validator-core';
import axios from 'axios'
import Swal from 'sweetalert2'
import Loader from "react-loader-spinner";

import InputValidator from './InputValidator';
import { MainContext } from './Context';


class SignUp extends Component {
    constructor(props) {
        super(props)
    
        this.state = {
            name:'',
            email:'',
            password:'',
            disabledBtn:false,
       }
       this._onInputChange = this._onInputChange.bind(this);
    }

    _onInputChange = (e)=>{
        this.setState({[e.target.name]:e.target.value});
    }
    clearInput = ()=>{
        this.setState({name:'',email:'',password:''})
    }
    _onSubmit = async() => {

        // show progress indicator
        this.setState({disabledBtn:true});
        if(navigator.onLine){
            try{
                const params={
                        name : this.state.name,
                        email: this.state.email,
                        password: this.state.password
                }
                
                const response = await axios.post('/SignUp',params);
                //console.log(response);
                if(response.status === 200){
                    if(response.data.status === true){
                        this.clearInput();
                        Swal.fire({
                            title: "Success!",
                            text:"Account Created Successfully!",
                            icon:"success",
                            confirmButtonText: 'Login'
                        })
                        .then((result)=>{
                            if(result.isConfirmed){
                                this.props.history.push("/login");
                            }
                        })
                        

                    }
                    else
                        Swal.fire("Failed!", response.data.message, "error");


                }else{
                    //request status is not  200
                    Swal.fire("Oops!",'Something went wrong', "error");
                }
            }catch(error){
                //console.log(error.message);
                Swal.fire("Oops!", "Network Error", "error");
            }
        }else{
            //if network is not connected
            Swal.fire("No Internet!", "Please check your network connection!", "error");
        }
        this.setState({disabledBtn:false});
}

    render() {

        if(this.context.state.auth.isLoggedIn){

            return <Redirect to="/dashboard"/>;
        }

        const {name,email,password} = this.state;
        return (
            <div id="login" className="position-relative">
                <div className="login-container position-absolute bg-white shadow-md">

                    <ValidatorForm 
                        ref="form"
                        onSubmit={this._onSubmit}
                    >
                        <InputValidator
                            label="Name"
                            iconClass="bi bi-person"
                            onChange={this._onInputChange}
                            name="name"
                            className="form-control"
                            value={name}
                            placeholder="Enter your full name"
                            validators={['required','matchRegexp:^[a-zA-Z]+ [a-zA-Z]+$']}
                            errorMessages={['This field is required', 'Name is not valid']}
                        />

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
                            validators={['required','minStringLength:5','maxStringLength:20']}
                            errorMessages={['This field is required','Minimum password length 5','Maximum password length is 20']}
                        />

                        <div className="text-center">
                            <button className={(this.state.disabledBtn?"btn btn-primary disabled":"btn btn-primary")}>
                                {this.state.disabledBtn?<Loader type="ThreeDots" color="#FFF" height={25} width={40} />: 'Sign Up'}
                            </button>
                        </div>
                        <div className="mt-3 text-center">
                            <p>Already have an account? <Link to="/login" className="text-decoration-none">Login</Link></p>
                        </div>
                    </ValidatorForm>
                </div>
                
            </div>
        )
    }
}
SignUp.contextType = MainContext;
export default withRouter(SignUp);