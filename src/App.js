
import "react-loader-spinner/dist/loader/css/react-spinner-loader.css";
import React from 'react';

import {
  BrowserRouter as Router,
  Switch,
  Route
} from "react-router-dom";
import Cookies from 'universal-cookie'
import axios from 'axios'

import Dashboard from './screens/Dashboard';
import LoginScreen from './screens/LoginScreen';
import SignUpScreen from './screens/SignUpScreen';
import HomePage from './component/HomePage';
import { MainContext } from './component/Context';
import NotFound from './screens/NotFound';
import LoadingScreen from "./screens/LoadingScreen";


const routes = [
  {
    path: "/",
    exact: true,
    main: () => <HomePage/>
  },
  {
    path: "/login",
    main: (props) => <LoginScreen/>
  },
  {
    path: "/signup",
    main: () => <SignUpScreen/>
  },
  {
    path: "/dashboard",
    exact: true,
    main: () => <Dashboard/>
  },
  {
    path: "/dashboard",
    exact: false,
    main: () => <Dashboard/>
  },
  {
    path: "",
    main: () => <NotFound/>
  }
];
class App extends React.Component {
  constructor(props) {
    super(props)
  
    this.state = {
       showLoading:true,
       auth:{
        isLoggedIn: false,
        token: false
	    }
    }
  }


  componentDidMount(){
	this.checkLoginStatus();
  setTimeout(()=>{
      this.setState({showLoading:false});
    },3000);
  }

  checkLoginStatus = async()=>{
	const cookies = new Cookies();
	let authToken = await cookies.get('token');
	if(authToken){

    this.setState({
      auth:{...this.state.auth, token:authToken}
    });

    //console.log(this.state.auth.token);
		try{
			const response = await axios.post('/CheckToken',
				{},
				{
					headers:{
						"Authorization" : `Bearer ${this.state.auth.token}`
					}
				}
			);

			if(response.status === 200){
				if(response.data.status){
					this.setState({auth:{...this.state.auth,isLoggedIn:true}});
				}else{
					this.setState({auth:{...this.state.auth,isLoggedIn:false}});
				}
			}
		}catch(e){
			console.log('Refresh the page');
		}
	}
  }

  /**
   * to set state variable 
   * 
   * @param {*} key 
   * @param {*} val 
   */
  updateState = (key, val) => {
	this.setState({[key]: val});
 }


  render(){
    return (
      <>
      <LoadingScreen show={this.state.showLoading}/>
      <MainContext.Provider
        value={{state:this.state,updateState:this.updateState}}
      >
        <div className="App">
          <Router>
            <Switch>
              {
              routes.map((route, index) => (
                <Route
                key={index}
                path={route.path}
                exact={route.exact}
                children={ <route.main/> }
                />
              ))}
            </Switch>
          </Router>
        </div>
      </MainContext.Provider>
      </>
    );
  }
}

export default App;
