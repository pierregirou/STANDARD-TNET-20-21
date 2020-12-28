import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Observable, of, from, throwError } from 'rxjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Injectable({
  providedIn: 'root'
})
export class TelechargementService {

  baseUrl:string=''

  constructor(private httpClient:HttpClient,private httpRequest:HttpRequest, private snackBar:MatSnackBar) {
    this.baseUrl = '../';
  }

  postFile(fileToUpload: File, arrayParams) {
    const formData: FormData = new FormData();
    formData.append('multimediaBox', fileToUpload);
    formData.append('action', 'uploadFile');
    formData.append('telType', arrayParams["type"]);
    formData.append('telIntitule', arrayParams["intitule"]);
    formData.append('telLien', arrayParams["lien"]);
      this.httpClient.post(this.httpRequest.uploadFile, formData).subscribe(
        (response:string) => {
          var position = response.indexOf("Ajoute a la base de donnee. ");
          if (position > 0) {
            this.snackBar.open("PDF ajouté !","x",{
              duration:3000
            });
          }
        },
        (error) => console.log(error)
      );
}

  getTelechargement(){
    return new Promise(
        (resolve,reject)=>{
            this.httpClient.post(this.httpRequest.Telechargement,{
                "login":sessionStorage.getItem("loginCompte")
            }).subscribe(data=>{
              resolve(data);
            });
        }
    );
  }

  getTelechargementList(){
    return new Promise(
        (resolve,reject)=>{
            this.httpClient.post(this.httpRequest.TelechargementFile,{
                "login":sessionStorage.getItem("loginCompte"),
                "type":"list"
            }).subscribe(data=>{
              resolve(data);
            });
        }
    );
  }

}
