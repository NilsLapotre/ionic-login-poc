import { IonContent,IonRow, IonGrid, IonHeader, IonPage, IonTitle, IonToolbar, IonInput, IonItem, IonCol, IonLabel, IonAlert, IonButton } from '@ionic/react';
import React, { useState } from 'react';
import './Login.css';


const Login: React.FC = () => {
  const [email, setEmail] = useState<string>();
  const [iserror, setIserror] = useState<boolean>(false);
  const [message, setMessage] = useState<string>("");
  const [password, setPassword] = useState<string>();

  const validateEmail = () => {
    var regexp = new RegExp('.+\@.+\..+');
    if (regexp.test(email!)) {
      return true;
    }
    return false;
  }

  const handleLogin = () => {
    if (!email) {
        setMessage("Please enter a valid email");
        setIserror(true);
        return;
    }
    if (validateEmail() === false) {
        setMessage("Your email is invalid");
        setIserror(true);
        return;
    }

    if (!password) {
        setMessage("Please enter your password");
        setIserror(true);
        return;
    }

  };


  return (
    <IonPage>
    <IonHeader>
      <IonToolbar>
        <IonTitle>Login</IonTitle>
      </IonToolbar>
    </IonHeader>
    <IonContent fullscreen className="ion-padding ion-text-center">
      <IonGrid>
      <IonRow>
        <IonCol>
          <IonAlert
              isOpen={iserror}
              onDidDismiss={() => setIserror(false)}
              cssClass="my-custom-class"
              header={"Error!"}
              message={message}
              buttons={["Dismiss"]}
          />
        </IonCol>
      </IonRow>
        <IonRow>
          <IonCol>
          <IonItem>
          <IonLabel position="floating"> Email</IonLabel>
          <IonInput
              type="email"
              value={email}
              onIonChange={(e)=> { setEmail(e.detail.value!)}}
              >
          </IonInput>
          </IonItem>
          </IonCol>
        </IonRow>

        <IonRow>
          <IonCol>
          <IonItem>
            <IonLabel position="floating"> Password</IonLabel>
            <IonInput
              type="password"
              value={password}
              onIonChange={(e)=> { setPassword(e.detail.value!)}}
              >
            </IonInput>
          </IonItem>
          </IonCol>
        </IonRow>
        <IonRow>
          <IonCol>
            <IonButton expand="block" onClick={handleLogin}
            >Login</IonButton>
            <p style={{ fontSize: "medium" }}>
                Forgot your password ? <a href="#">Click here</a>
            </p>

          </IonCol>
        </IonRow>
      </IonGrid>
    </IonContent>
  </IonPage>
);
};
export default Login;
