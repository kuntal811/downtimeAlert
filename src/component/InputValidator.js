import React from 'react';
import { ValidatorComponent } from 'react-form-validator-core';

class InputValidator extends ValidatorComponent {
    constructor(props) {
        super(props)
    
        this.state = {
             isValid:true
        }
    }
    
    renderValidatorComponent() {
        const { label,iconClass, errorMessages, validators, requiredError, validatorListener,onError, ...rest } = this.props;
        
        return (
                <div className="mb-3">
                <label htmlFor={label} className="form-label">{label}</label>
                <div className="input-group">
                    <span className="input-group-text"><i className={iconClass}/></span>
                    <input
                        {...rest}
                        id={label}
                        ref={(r) => { this.input = r; }}
                    />
                </div>
                <small className="text-danger" style={{display:'block !important'}}>
                    {this.errorText()}
                </small>
                </div>
        );
    }

    errorText() {
        const { isValid } = this.state;

        if (isValid) {
            return null;
        }

        return this.getErrorMessage();

    }
}

export default InputValidator;