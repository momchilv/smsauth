function ErrorMessage(props) {
    return (
        <ul className="errors-list">
            {Object.keys(props.value).map((item, index) => {
                return (props.value[item] != '') ? <li key={index}>{props.value[item]}</li> : null
            })}

        </ul>
    );
}


export default ErrorMessage;