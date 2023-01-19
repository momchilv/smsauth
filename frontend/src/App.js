import React from 'react';
import './SmsAuth.css';

import AuthForm from './components/AuthForm';
import VerificationCode from './components/VerificationCode';
import ErrorMessage from './components/ErrorMessage';


class SmsAuth extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      email: '',
      phone: '',
      password: '',
      verification_code: '',
      token: '',
      show_verification_code_input: false,
      show_resend_verification_code_link: false,
      show_resend_success_message: false,
      disable_submit: false,
      errors: {
        email: '',
        phone: '',
        password: '',
        verification_code: '',
        general: ''
      },
      verification_code_attemps: 0,
      fields_to_validate: [],

      submited_auth: false,
      submited_verification_code: false

    };

    this.handleChange = this.handleChange.bind(this);
    this.handleBlur = this.handleBlur.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
  }

  handleChange(event) {
    this.setState({ ...this.state, [event.target.attributes['rel'].value]: event.target.value },
      () => {

        this.isValid(event.target.attributes['rel'].value);
      }
    );
  }

  handleBlur(event) {
    let fields_to_validate = this.state.fields_to_validate.slice();
    if (!this.state.fields_to_validate.includes(event.target.attributes['rel'].value)) {
      this.state.fields_to_validate.push(event.target.attributes['rel'].value);
      fields_to_validate.push(event.target.attributes['rel'].value);
    }

    this.setState({ ...this.state, fields_to_validate: fields_to_validate },
      () => {
        this.isValid(event.target.attributes['rel'].value);
      }
    );
  }

  handleSubmit(event) {
    event.preventDefault();
    if (!this.state.show_verification_code_input) {
      this.submitSignUp();
    } else {
      this.submitVerificationCode();
    }
  }

  submitSignUp() {
    this.state.fields_to_validate = ['email', 'phone', 'password'];
    this.setState({ ...this.state, fields_to_validate: ['email', 'phone', 'password'] });
    if (this.validateSignUpFields()) {
      let data = new FormData();
      data.append('api_edge', 'signup');
      data.append('email', this.state.email);
      data.append('phone', this.state.phone);
      data.append('password', this.state.password);

      this.sendRequest(data).then((result) => {
        if (result.verification_code_sent == 1) {
          this.setState({ token: result.token, show_verification_code_input: true });
          setTimeout(() => this.setState({ show_resend_verification_code_link: true }), 60000);
        }
        else {
          this.setStatusErrors(result);
        }
      }, (error) => {
        this.setStatusErrors(error);
      });
    }
  }

  submitVerificationCode() {
    if (this.state.disable_submit) {
      return;
    }
    const verificationCode = this.state.verification_code.replace(/[^0-9.]/g, '');
    if (verificationCode.length == 6) {
      let data = new FormData();
      data.append('api_edge', 'verify_user');
      data.append('verification_code', verificationCode);
      this.sendRequest(data).then((result) => {
        if (result.success == 1) {
          document.location = 'http://localhost:8000/index.html';
        } else {
          if (typeof result.verification_code_cooldown != 'undefined') {
            this.setState({ disable_submit: true }, () => { setTimeout(() => this.setState({ disable_submit: false }), 60000); });// cooldown 1 min});              
          }
          this.setStatusErrors(result);
        }
      }, (error) => {
        this.setStatusErrors(error);
      });
    } else {
      this.setStatusErrors({ error_msg: "Invalid code!", error_field: 'verification_code' });
    }
  }

  resendVerificationCode(event) {
    event.preventDefault();
    let data = new FormData();
    data.append('api_edge', 'resend_verification_code');
    this.sendRequest(data).then((result) => {
      if (result.verification_code_resent == 1) {
        this.setState({ show_verification_code_input: true, show_resend_verification_code_link: false, show_resend_success_message: true });
        setTimeout(() => this.setState({ show_verification_code_input: true, show_resend_verification_code_link: true, show_resend_success_message: false }), 60000);
      }
    }, (error) => {
      this.setStatusErrors(error);
    });
  }

  validateSignUpFields() {
    let valid = this.isValid('email');
    valid = this.isValid('phone') && valid;
    valid = this.isValid('password') && valid;
    return valid;
  }

  isValid(field) {
    if (!this.state.fields_to_validate.includes(field)) {
      return true;
    }

    let errors = { ...this.state.errors };
    let regEx;
    switch (field) {
      case 'email': {
        regEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        break;
      }
      case 'phone': {
        this.state.phone = this.state.phone.replace(/[^0-9.]/g, '');
        regEx = /^(359|0)[0-9]{9}$/;
        break;
      }
      case 'password': {
        regEx = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        break;
      }
    }

    if (!regEx.test(this.state[field])) {
      errors[field] = field.charAt(0).toUpperCase() + field.slice(1) + ' is not valid!';
    }
    else {
      errors[field] = '';
    }

    this.setState({ errors: errors });
    this.state.errors = { ...errors };
    return errors[field] == '';
  }

  sendRequest(data) {
    if (this.state.token) {
      data.append('token', this.state.token);
    }

    return fetch("http://localhost:8000/" + data.get('api_edge'), {
      method: "POST",
      body: data
    }).then(res => res.json());

  }

  setStatusErrors(error) {
    let errors = { ...this.state.errors }
    if (typeof error.error_field != 'undefined') {
      errors[error.error_field] = error.error_msg;
    }
    else {
      errors['general'] = error.error_msg;
    }

    this.setState({ errors: errors });
  }


  render() {
    return (
      <div className="form-wrapper">
        <h4><label className="title">Sign up with phone verification</label></h4>
        <form onSubmit={this.handleSubmit}>
          {this.state.show_verification_code_input ?
            <VerificationCode errors={this.state.errors.verification_code} onChange={(e) => this.handleChange(e)} onBlur={(e) => this.handleBlur(e)} />
            :
            <AuthForm errors={this.state.errors} onChange={(e) => this.handleChange(e)} onBlur={(e) => this.handleBlur(e)} />
          }
          {(Object.keys(this.state.errors).some(errType => this.state.errors[errType] != '')) ? <ErrorMessage value={this.state.errors} /> : null}
          <input type="submit" disabled={this.state.disable_submit} value={this.state.show_verification_code_input ? "Log in" : "Sign up"} />
        </form>
        {this.state.show_verification_code_input && this.state.show_resend_verification_code_link ?
          <a className="resend-link" onClick={(e) => this.resendVerificationCode(e)}> Resend verification code</a> : null
        }
        {this.state.show_verification_code_input && this.state.show_resend_success_message ?
          <p className="resend-success-message">Verification code is resent</p> : null
        }
      </div>
    );
  }
}

export default SmsAuth;
