export function validEmail (email:string) {
  const reg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(?!qq\.com)((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return reg.test(email.toLocaleLowerCase());
}

export function validPwd (pwd:string) {
  return true;
}

export function validUserName (username:string) {
  return (username.length >= 2 && username.length <= 8);
}