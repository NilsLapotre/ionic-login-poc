import { IonContent,IonRow, IonGrid, IonHeader, IonPage, IonTitle, IonToolbar, IonInput, IonItem, IonCol, IonLabel, IonAlert, IonButton } from '@ionic/react';
import React, { useState } from 'react';
import { RequestPassword } from '../services/passwordService'

const ForgotPassword: React.FC = () => {
  const [email, setEmail] = useState<string>();
  const [iserror, setIserror] = useState<boolean>(false);
  const [isopen, setIsopen] = useState<boolean>(false);
  const [message, setMessage] = useState<string>("");
 
  const validateEmail = () => {
    var regexp = new RegExp('.+\@.+\..+');
    if (regexp.test(email!)) {
      return true;
    }
    return false;
  }

  const handleSubmit = () => {
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

   //Appel vers service
    RequestPassword(email)
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
        <IonTitle>Forgot password</IonTitle>
      </IonToolbar>
    </IonHeader>
    <IonContent fullscreen className="ion-padding ion-text-center">
      <IonGrid >
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
          onDidDismiss={() => setIsopen(false)}
          header={"Success!"}
          message={"An email was send to this address"}
          buttons={["Ok"]}
          />
        </IonCol>
      </IonRow>
      <IonRow className="ion-justify-content-center">
          <IonCol size-lg="4" size-sm="10" className="ion-align-self-center">
            <IonLabel position="floating"> Enter your email to reset your password</IonLabel>    
          </IonCol>
        </IonRow>
        <IonRow className="ion-justify-content-center">
          <IonCol size-lg="4" size-sm="10" className="ion-align-self-center">
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
        <IonRow className="ion-justify-content-center">
          <IonCol size-lg="4" size-sm="10" className="ion-align-self-center">
            <IonButton expand="block" onClick={handleSubmit}>Envoyer</IonButton>
           </IonCol>
        </IonRow>
      </IonGrid>
    </IonContent>
  </IonPage>
);
};
export default ForgotPassword;
