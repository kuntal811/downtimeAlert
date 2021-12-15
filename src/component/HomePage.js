import React, { Component } from 'react'
import { withRouter } from 'react-router'
import FeaturesCard from './FeaturesCard'


import Header from './Header'
class HomePage extends Component {


    render() {
            return (
                <>
                <Header/>
                <div id="content">

                    <section id="hero" className="container">
                        <div className="row my-5 justify-content-center">
                            <div className="col-md-6 d-flex align-items-center justify-content-center order-md-1 order-2">
                                <div className="hero-text px-5">
                                    <h2 className="mt-0 mb-3">We help you find the best solution </h2>
                                    <p>
                                        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et vehicula amet lobortis platea interdum. Tortor faucibus mi eu volutpat, leo odio euismod sapien. Ut vitae aliquam quis facilisi sagittis tortor, et urna.</small>
                                    </p>
                                    <button className="mt-5 btn btn-primary" onClick={()=>this.props.history.push("/SignUp")}>Start Monitoring</button>
                                </div>
                            </div>

                            <div className="col-md-6 order-md-2 order-1">
                                <img src="images/hero.png" className="img-fluid" height="500" alt="downtime illustration"/>
                            </div>
                        </div>
                    </section>



                    <section id="features" className="pt-1 pb-5 bg-primary">
                        <div className="container">
                            <h2 className="my-5 section-header text-center text-capitalize text-white">What We Offer</h2>
                            <div className="row mt-5">
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/server.svg"
                                        title="Server Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/domain.svg"
                                        title="Domain Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/certificate.svg"
                                        title="SSL Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>
                            </div>
                            {/*}
                            <div className="row mt-4">
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/server.svg"
                                        title="Downtime Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/server.svg"
                                        title="Downtime Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>
                                <div className="col-md-4 px-lg-4 px-md-2 px-4 mb-3">
                                    <FeaturesCard
                                        image="images/server.svg"
                                        title="Downtime Monitoring"
                                        description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et veh"
                                    />
                                </div>                 
                            </div>
                        */}
                        </div>
                    </section>




                    <section id="main-features" className="bg-white py-5 px-3">
                        <div className="container">
                            <div className="row justify-content-center my-5">
                                <div className="col-md-6 order-2 order-md-1">
                                    <h3 className="text-left">Downtime monitoring that ensures you're the first to know</h3>
                                    <p className="text-left">
                                        <small>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et vehicula amet lobortis platea interdum. Tortor faucibus mi eu volutpat, leo odio euismod sapien. Ut vitae aliquam quis facilisi sagittis tortor.
                                        </small>
                                    </p>
                                </div>
                                <div className="col-md-6 order-1 order-md-2">
                                    <img src="images/server.svg" className="d-block mx-auto" height="150" alt=""></img>
                                </div>
                            </div>
                            <div className="row justify-content-center my-5">
                                <div className="col-md-6 order-2 order-md-2">
                                    <h3 className="text-left">Domain monitoring that keeps your identity safe</h3>
                                    <p className="text-left">
                                        <small>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et vehicula amet lobortis platea interdum. Tortor faucibus mi eu volutpat, leo odio euismod sapien. Ut vitae aliquam quis facilisi sagittis tortor.
                                        </small>
                                    </p>
                                </div>
                                <div className="col-md-6 order-1 order-md-1">
                                    <img src="images/domain.svg" className="d-block mx-auto" height="150" alt=""></img>
                                </div>
                            </div>
                            <div className="row justify-content-center mt-5">
                                <div className="col-md-6 order-2 order-md-1">
                                    <h3 className="text-left">Rank better and build customer trust with SSL certification</h3>
                                    <p className="text-left">
                                        <small>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Amet lobortis a et vehicula amet lobortis platea interdum. Tortor faucibus mi eu volutpat, leo odio euismod sapien. Ut vitae aliquam quis facilisi sagittis tortor.
                                        </small>
                                    </p>
                                </div>
                                <div className="col-md-6 order-1 order-md-2">
                                    <img src="images/certificate.svg" className="d-block mx-auto" height="150" alt=""></img>
                                </div>
                            </div>
                        </div>
                    </section>



                    <section id="get-started" style={{backgroundColor:'#A855F744'}} className="pt-5 pb-5">
                        <div className="container text-center">
                            <h4 className="mb-3">Ready to Get Started</h4>
                            <p className="m-0">Take control of your website.</p>
                            <p>Try Downtime Alert to make your business better</p>
                            <button className="my-3 mt-4 btn btn-primary" onClick={()=>this.props.history.push("/SignUp")}>Start Monitoring</button>
                        </div>
                    </section>




                </div>
                <footer className="p-3 bg-primary">
                    <p className="text-center m-0 text-white">&copy; {new Date().getFullYear()} Downtime Alert. All right Reserved</p>
                </footer>
                </>

            )
    }
}

export default withRouter(HomePage)