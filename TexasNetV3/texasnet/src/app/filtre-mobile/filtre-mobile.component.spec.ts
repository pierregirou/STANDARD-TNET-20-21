import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FiltreMobileComponent } from './filtre-mobile.component';

describe('FiltreMobileComponent', () => {
  let component: FiltreMobileComponent;
  let fixture: ComponentFixture<FiltreMobileComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FiltreMobileComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FiltreMobileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
