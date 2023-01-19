function VerificationCode(props) {
    return (
        <label>
            Verification code:
            <input type="text" rel="verification_code" onChange={props.onChange} className={props.errors != '' ? 'highlight-error-input' : ''} />
        </label>
    );
}


export default VerificationCode;