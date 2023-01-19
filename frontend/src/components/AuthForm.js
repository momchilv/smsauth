function AuthForm(props) {
    return (
        <div className="signup-fields">
            <label>Email:</label>
            <input type="text" rel="email" onChange={props.onChange} onBlur={props.onBlur} className={props.errors.email != '' ? 'highlight-error-input' : ''} />
            <br />
            <label>Phone:</label>
            <input type="text" rel="phone" onChange={props.onChange} onBlur={props.onBlur} className={props.errors.phone != '' ? 'highlight-error-input' : ''} />
            <br />
            <label>Password:</label>
            <input type="password" rel="password" onChange={props.onChange} onBlur={props.onBlur} className={props.errors.password != '' ? 'highlight-error-input' : ''} />
        </div>
    );
}


export default AuthForm;