import React from 'react'

function Footer(props) {
    return (
        <footer className="w-100" style={props.isSidebarOpen?{paddingLeft:'250px'}:{paddingLeft:'0px'}}>
            <div className="footer d-flex justify-content-between">
                <div className="footer-left">
                    <p><b>Copyright &copy; 2021 Kuntal Sarkar</b>. All rights reserved</p>
                </div>
                <div className="footer-right">
                    <p><b>Version</b>1.0.0</p>
                </div>
            </div>
        </footer>
    )
}

export default Footer
