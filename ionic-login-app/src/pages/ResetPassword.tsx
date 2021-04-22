import { IonContent,IonRow, IonGrid, IonHeader, IonPage, IonTitle, IonToolbar, IonInput, IonItem, IonCol, IonLabel, IonAlert, IonButton } from '@ionic/react';
import React, { useState } from 'react';
import { RouteComponentProps } from 'react-router';
import { useHistory } from "react-router-dom";
import { UpdatePassword } from '../services/passwordService'


interface ResetPasswordProps extends RouteComponentProps<{
    token: string;
  }> {}

const ResetPassword: React.FC<ResetPasswordProps> = ({match})  => {
  const [password, setPassword] = useState<string>();
  const [confirmPassword, setConfirmPassword] = useState<string>();
  const [iserror, setIserror] = useState<boolean>(false);
  const [isopen, setIsopen] = useState<boolean>(false);
  const [message, setMessage] = useState<string>("");
  const history = useHistory();

    const goToLogin= () => {
        setIsopen(false);
        history.push("/login");
    }
 
 
  const handleSubmit = () => {
    if (!password || !confirmPassword) {
        setMessage("Please fill both fields");
        setIserror(true);
        return;
    }
    if (password !== confirmPassword) {
        setMessage("Passwords don't match");
        setIserror(true);
        return;
    }
    const token = match.params.token;
   //Appel vers service
   UpdatePassword(token, password)
    .then(res => {
        setIsopen(true);
    })
    .catch(error => {
        setMessage(error);
        setIserror(true);
    })
};


  return (
    <IonPage>
    <IonHeader>
      <IonToolbar>
        <IonTitle>Reset password</IonTitle>
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
          <IonAlert
          isOpen={isopen}
          onDidDismiss={() => goToLogin()}
          header={"Success!"}
          message={"Password was succesfully updated"}
          buttons={["Login"]}
          />
        </IonCol>
      </IonRow>
      <IonRow>
          <IonCol>
            <IonLabel position="floating"> Please enter your new password </IonLabel>    
          </IonCol>
        </IonRow>
        <IonRow>
          <IonCol>
          <IonItem>
          <IonLabel position="floating"> Password </IonLabel>
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
          <IonItem>
          <IonLabel position="floating"> Confirm password</IonLabel>
          <IonInput
              type="password"
              value={confirmPassword}
              onIonChange={(e)=> { setConfirmPassword(e.detail.value!)}}
              >
          </IonInput>
          </IonItem>
          </IonCol>
        </IonRow>
        <IonRow>
          <IonCol>
            <IonButton expand="block" onClick={handleSubmit}>Envoyer</IonButton>
           </IonCol>
        </IonRow>
      </IonGrid>
    </IonContent>
  </IonPage>
);
};
export default ResetPassword;
