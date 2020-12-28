import { Component, OnInit } from '@angular/core';
import { ImageService } from "../../services/images.service";
import { AuthService } from "../../services/auth.service";
import { Router } from '@angular/router';

@Component({
  selector: 'app-menu-administration',
  templateUrl: './menu-administration.component.html',
  styleUrls: ['./menu-administration.component.css']
})
export class MenuAdministrationComponent implements OnInit {

  constructor(public imageService:ImageService,private authService:AuthService,private router:Router) { }

  ngOnInit() {
  }

  deconnexion(){
    this.authService.logOut();
    this.router.navigate(['']);
  }

}
