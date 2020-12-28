import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MenuAdministrationComponent } from './menu-administration.component';

describe('MenuAdministrationComponent', () => {
  let component: MenuAdministrationComponent;
  let fixture: ComponentFixture<MenuAdministrationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MenuAdministrationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MenuAdministrationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
